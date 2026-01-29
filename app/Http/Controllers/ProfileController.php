<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:20', 
            'address' => 'required|string|max:255', 
            'current_password' => 'nullable|string',
            'new_password'     => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'address.required' => 'Vui lòng chọn đầy đủ Tỉnh/Thành, Phường/Xã và nhập số nhà.',
        ]);

        $user->name = $request->name;

        if ($request->filled('current_password') || $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
            }

            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        $user->customerProfile()->updateOrCreate(
            ['user_id' => $user->id], 
            [
                'full_name'=> $request->name,
                'gender'  => $request->gender,
                'phone'   => $request->phone,
                'address' => $request->address,
            ]
        );

        return redirect()->back()->with('success', 'Cập nhật hồ sơ thành công!');
    }
}