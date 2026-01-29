@extends('layouts.app')

@section('title', 'Đăng ký - ' . config('app.name'))
@section('content')

<div class="register-page">
    <div class="register-form-section">
        <div class="register-container slide-up">
            <div class="form-header">
                <h2 class="form-title">Đăng ký</h2>
            </div>

            <form method="POST" action="{{ route('register') }}" class="register-form slide-right">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label">Họ tên</label>
                        <input type="text"
                            id="name"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Nhập họ tên"
                            value="{{ old('name') }}"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email"
                            id="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Nhập email"
                            autocorrect="off"
                            spellcheck="off">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-control pe-5 @error('password') is-invalid @enderror"
                            placeholder="Tối thiểu 8 ký tự"
                            autocorrect="off"
                            spellcheck="off"
                            required>
                        <div class="password-toggle" onclick="togglePassword('password')" aria-label="Hiển thị/Ẩn mật khẩu">
                            <span id="password-eye"><i class='far fa-eye'></i></span>
                        </div>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter">
                            <div class="strength-bar" id="strength-bar"></div>
                        </div>
                        <small class="strength-text" id="strength-text">Độ mạnh mật khẩu</small>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password-confirm" class="form-label">Xác nhận mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password"
                            id="password-confirm"
                            name="password_confirmation"
                            class="form-control pe-5"
                            placeholder="Nhập lại mật khẩu"
                            required>
                        <div class="password-toggle" onclick="togglePassword('password-confirm')" aria-label="Hiển thị/Ẩn mật khẩu">
                            <span id="password-confirm-eye"><i class='far fa-eye'></i></span>
                        </div>
                    </div>
                    <div class="password-match">
                        <small class="match-text" id="match-text"></small>
                    </div>
                </div>

                <!-- Terms Checkbox -->
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="terms" id="terms" required>
                    <label class="form-check-label" for="terms">
                        Tôi đồng ý với <a href="#" target="_blank">Điều khoản sử dụng</a> và <a href="#" target="_blank">Chính sách bảo mật</a>
                    </label>
                </div>


                <button type="submit" class="btn-register" id="btnRegister">
                    <span class="btn-text">Đăng ký</span>
                    <span class="btn-loading d-none">
                        <span class="btn-spinner"></span>
                        Đang xử lý...
                    </span>
                </button>

                <div class="login-section">
                    <p class="login-text">
                        Đã có tài khoản?
                        <a href="{{ route('login') }}" class="login-link">Đăng nhập ngay</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('js/register.js') }}"></script>
@endpush

