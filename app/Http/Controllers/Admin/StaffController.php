<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $permissions = Permission::where('name', 'not like', 'staff.%')
            ->where('name', 'not like', 'revenue.%')
            ->get();

        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $staffs = User::role(['staff', 'manager'])
            ->with(['staffProfile', 'permissions'])
            ->latest()
            ->paginate($perPage);

        return view('admin.staffs.index', compact('staffs', 'permissions'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.staffs.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'full_name' => 'required|string',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->full_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            $user->assignRole('staff');

            $user->staffProfile()->create([
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'position' => $request->position,
                'salary' => $request->salary,
                'start_date' => $request->start_date,
                'status' => 'active',
            ]);

            if ($request->has('permissions')) {
                $user->syncPermissions($request->permissions);
            }
        });

        return redirect()->route('admin.staffs.index')->with('success', 'Thêm mới nhân viên.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $staff)
    {
        $this->authorize('view', $staff);
        $permissions = Permission::where('name', 'not like', 'staff.%')
            ->where('name', 'not like', 'revenue.%')
            ->get();
        $staff->load('staffProfile', 'permissions');

        return view('admin.staffs.show', compact('staff', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $staff)
    {
        $staff->load('staffProfile', 'permissions');
        $permissions = Permission::where('name', 'not like', 'staff.%')->get();

        return view('admin.staffs.edit', compact('staff', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $staff)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'full_name' => 'required|string',
            'permissions' => 'array',
        ]);

        DB::transaction(function () use ($request, $staff) {
            $dataUser = ['email' => $request->email, 'name' => $request->full_name];
            if ($request->filled('password')) {
                $dataUser['password'] = Hash::make($request->password);
            }
            $staff->update($dataUser);

            $staff->staffProfile()->updateOrCreate(
                ['user_id' => $staff->id],
                [
                    'full_name' => $request->full_name,
                    'phone' => $request->phone,
                    'position' => $request->position,
                    'salary' => $request->salary,
                    'start_date' => $request->start_date,
                    'status' => $request->status ?? 'active',
                ]
            );
            $staff->syncPermissions($request->input('permissions', []));
        });

        return back()->with('success', 'Cập nhật nhân viên.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $staff)
    {
        $this->authorize('delete', $staff);
        $staff->delete();

        return back()->with('success', 'Xóa nhân viên.');
    }
}
