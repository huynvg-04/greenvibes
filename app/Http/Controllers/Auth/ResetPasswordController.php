<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    use \Illuminate\Foundation\Auth\ResetsPasswords {
        resetPassword as protected defaultResetPassword;
    }

    protected function redirectTo()
    {
        return '/login';
    }

    protected function resetPassword($user, $password)
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        session()->flash('success', 'Thay đổi mật khẩu thành công!');
    }
}
