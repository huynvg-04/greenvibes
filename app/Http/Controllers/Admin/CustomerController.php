<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\User;
use App\Models\MembershipTier;
use App\Policies\CustomerPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $tiers = MembershipTier::all();

        $query = User::role('customer')
            ->with('customerProfile.tier'); 

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('email', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhereHas('customerProfile', function ($subQ) use ($keyword) {
  
                        $subQ->where('full_name', 'like', '%' . $keyword . '%')
                             ->orWhere('phone', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('gender')) {
            $query->whereHas('customerProfile', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        if ($request->filled('tier_id')) {
            $query->whereHas('customerProfile', function ($q) use ($request) {
                $q->where('membership_tier_id', $request->tier_id);
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('status', 'active'); 
            } elseif ($request->status == 'locked') {
                $query->where('status', 'blocked');
            }
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'desc':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->latest();
        }

        
        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        $customers = $query->paginate($perPage)->appends($request->all());

        return view('admin.customers.index', compact('customers', 'tiers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', User::class);

        return view('admin.customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:male,female,other',
            'phone'     => 'required|string|max:20',
            'address'   => 'nullable|string',
            'status'    => 'required|in:active,blocked',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->full_name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'status'   => $request->status,
            ]);

            $user->assignRole('customer');

            $defaultTier = MembershipTier::orderBy('rank_priority', 'asc')->first();

            $user->customerProfile()->create([
                'membership_tier_id' => $defaultTier ? $defaultTier->id : null,
                'full_name' => $request->full_name,
                'gender'    => $request->gender,
                'phone'     => $request->phone,
                'address'   => $request->address,
                'status'    => $request->status,
            ]);
        });

        return redirect()->route('admin.customers.index')->with('success', 'Thêm mới khách hàng.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $customer)
    {
        $customer->load('customerProfile');

        $this->authorize('update', $customer);

        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $customer)
    {
        $this->authorize('update', $customer);

        $request->validate([
            'email'     => 'required|email|unique:users,email,' . $customer->id,
            'full_name' => 'required|string|max:255',
            'gender'    => 'required|in:male,female,other',
            'phone'     => 'required|string|max:20',
            'status'    => 'required|in:active,blocked',
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        DB::transaction(function () use ($request, $customer) {
            $userData = [
                'email' => $request->email,
                'name'  => $request->full_name,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $customer->update($userData);

            $customer->customerProfile()->updateOrCreate(
                ['user_id' => $customer->id],
                [
                    'full_name' => $request->full_name,
                    'gender'    => $request->gender,
                    'phone'     => $request->phone,
                    'address'   => $request->address,
                    'status'    => $request->status,
                ]
            );
        });

        return redirect()->route('admin.customers.index')->with('success', 'Cập nhật khách hàng.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $customer)
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Xóa khách hàng.');
    }
}
