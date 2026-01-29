<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use App\Models\MembershipTier;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http; //


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showCustomerLoginForm()
    {
        return view('auth.customer-login');
    }

    public function customerLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Vui lòng nhập email.',
            'email.email'       => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $throttleKey = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            return back()->withInput($request->only('email'))->withErrors([
                'email' => "Tài khoản tạm khóa do nhập sai quá 5 lần. Vui lòng thử lại sau $minutes phút.",
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password) || ! $user->hasRole('customer')) {
            RateLimiter::hit($throttleKey, 1800);

            $remaining = RateLimiter::remaining($throttleKey, 5);
            
            return back()->withInput($request->only('email'))->withErrors([
                'email' => "Thông tin đăng nhập không chính xác. Bạn còn $remaining lần thử.",
            ]);
        }

        if (is_null($user->email_verified_at)) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Tài khoản của bạn chưa xác thực email. Vui lòng kiểm tra hộp thư đến (hoặc thư rác) để kích hoạt.',
            ]);
        }

        if ($user->status !== 'active') {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ hotline!',
            ]);
        }

        RateLimiter::clear($throttleKey);

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->intended('/')
            ->with('success', 'Đăng nhập thành công!');
    }



    // ============================
    // LOGIN CHO ADMIN
    // ============================
    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'g-recaptcha-response' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Gọi API Google để verify token
                    $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                        'secret' => env('GOOGLE_RECAPTCHA_SECRET'), // Lấy secret key từ file .env
                        'response' => $value,
                        'remoteip' => request()->ip()
                    ]);

                    if (!$response->json()['success']) {
                        $fail('Xác thực Captcha thất bại, vui lòng thử lại.');
                    }
                },
            ],
        ], [
            'g-recaptcha-response.required' => 'Vui lòng tích vào ô xác thực robot (Captcha).',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (method_exists($user, 'hasAnyRole') && $user->hasAnyRole(['manager', 'staff'])) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Đăng nhập thành công!');
            }

            Auth::logout();
            return back()->with('error', 'Bạn không có quyền truy cập vào trang quản trị!');
        }

        return back()->with('error', 'Thông tin đăng nhập không hợp lệ!');
    }


    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi kết nối Facebook: ' . $e->getMessage());
        }

        $profile = CustomerProfile::where('facebook_id', $facebookUser->getId())->first();

        if ($profile) {
            $user = $profile->user;
        } else {
            $email = $facebookUser->getEmail();

            if (!$email) {
                $email = $facebookUser->getId() . '@facebook.com';
            }

            if (User::where('email', $email)->exists()) {
                return redirect()->route('login')->with('error', 'Email này (' . $email . ') đã được đăng ký bằng Google hoặc Mật khẩu. Vui lòng đăng nhập bằng phương thức đó!');
            }

            $user = DB::transaction(function () use ($facebookUser, $email) {
                $newUser = User::create([
                    'name'            => $facebookUser->getName() ?? 'Facebook User',
                    'email'           => $email,
                    'password'        => bcrypt(Str::random(16)),
                    'status'          => 'active',
                    'email_verified_at' => now(),
                ]);

                if (method_exists($newUser, 'assignRole')) {
                    $newUser->assignRole('customer');
                }

                $defaultTier = MembershipTier::orderBy('rank_priority', 'asc')->first();

                $newUser->customerProfile()->create([
                    'full_name'   => $facebookUser->getName() ?? 'Facebook User',
                    'facebook_id' => $facebookUser->getId(),
                    'avatar'      => $facebookUser->getAvatar(),
                    'gender'      => 'other',
                    'status'      => 'active',
                    'membership_tier_id' => $defaultTier ? $defaultTier->id : null,
                ]);

                return $newUser;
            });
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ quản lý website!',
            ]);
        }

        Auth::login($user);
        return $this->checkProfileCompletion($user);
    }


    // ============================
    //  GOOGLE LOGIN
    // ============================
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Lỗi đăng nhập Google: ' . $e->getMessage());
        }

        $profile = CustomerProfile::where('google_id', $googleUser->getId())->first();

        if ($profile) {
            $user = $profile->user;
        } else {
            $email = $googleUser->getEmail() ?? $googleUser->getId() . '@google.com';

            if (User::where('email', $email)->exists()) {
                return redirect()->route('login')->with('error', 'Email này (' . $email . ') đã được sử dụng bởi Facebook hoặc tài khoản thường. Vui lòng đăng nhập bằng phương thức đó!');
            }

            $user = DB::transaction(function () use ($googleUser, $email) {
                $newUser = User::create([
                    'name'            => $googleUser->getName() ?? 'Google User',
                    'email'           => $email,
                    'password'        => bcrypt(Str::random(16)),
                    'status'          => 'active',
                    'email_verified_at' => now(),
                ]);

                if (method_exists($newUser, 'assignRole')) {
                    $newUser->assignRole('customer');
                }

                $defaultTier = MembershipTier::orderBy('rank_priority', 'asc')->first();

                $newUser->customerProfile()->create([
                    'full_name'   => $googleUser->getName() ?? 'Google User',
                    'google_id'   => $googleUser->getId(),
                    'avatar'      => $googleUser->getAvatar(),
                    'gender'      => 'other',
                    'status'      => 'active',
                    'membership_tier_id' => $defaultTier ? $defaultTier->id : null,
                ]);

                return $newUser;
            });
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa, vui lòng liên hệ quản lý website!',
            ]);
        }

        Auth::login($user);
        return $this->checkProfileCompletion($user);
    }


    protected function checkProfileCompletion($user)
    {
        $profile = $user->customerProfile;

        if (!$profile || empty($profile->phone) || empty($profile->address) || $profile->gender === 'other') {
            return redirect()->route('user.profile.edit')
                ->with('success', 'Đăng nhập thành công! Hãy hoàn tất hồ sơ của bạn để trải nghiệm tốt hơn.');
        }

        return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
    }
}
