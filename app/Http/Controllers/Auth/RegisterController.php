<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\MembershipTier;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Tên là bắt buộc.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Email không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
            ]);

            $defaultTier = MembershipTier::orderBy('rank_priority', 'asc')->first();

            CustomerProfile::create([
                'user_id' => $user->id,
                'full_name' => $data['name'],
                'status' => 'active',
                'membership_tier_id' => $defaultTier ? $defaultTier->id : null,
            ]);

            $user->assignRole('customer');
            
            return $user;
        });
    }

    protected function registered(Request $request, $user)
    {
        // event(new Registered($user));
        Auth::logout();

        return redirect()->route('login')
            ->with('success', 'Đăng ký thành công! Vui lòng kiểm tra email để xác thực trước khi đăng nhập.');
    }
}
