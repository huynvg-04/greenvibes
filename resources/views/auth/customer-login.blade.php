@extends('layouts.app')

@section('title', 'Đăng nhập - Green Vibes')
@section('content')

<div class="login-page">
    <div class="login-form-section">
        <div class="login-container slide-up">
            <div class="form-header">
                <h2 class="form-title">Đăng nhập</h2>
                <p class="form-subtitle">Chào mừng bạn quay trở lại Green Vibes</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-auto">
                <div class="alert-content">{{ session('success') }}</div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Đóng thông báo">×</button>
            </div>
            @endif

            @if(session('message'))
            <div class="alert alert-warning alert-auto">
                <div class="alert-content">{{ session('message') }}</div>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Đóng thông báo">×</button>
            </div>
            @endif

            <!-- @if ($errors->any())
            <div class="alert alert-danger">
                <div class="alert-content">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif -->

            <form method="POST" action="{{ route('login') }}" class="login-form slide-right">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Nhập địa chỉ email của bạn"
                        value="{{ old('email') }}"
                        required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mật khẩu</label>
                    <div class="input-wrapper">
                        <input type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu của bạn"
                            required>
                        <div class="password-toggle" onclick="togglePassword('password')" aria-label="Hiển thị/Ẩn mật khẩu">
                            <span id="password-eye"><i class='far fa-eye'></i></span>
                        </div>
                    </div>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-options">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <span class="btn-text">Đăng nhập</span>
                    <span class="btn-loading d-none">
                        <span class="btn-spinner"></span>
                        Đang đăng nhập...
                    </span>
                </button>
            </form>

            <div class="divider">
                <span class="divider-text">hoặc</span>
            </div>

            <div class="social-buttons">
                <a href="{{ url('dang-nhap/google') }}" class="btn-social btn-google">
                    Google
                </a>
                <a href="{{ url('dang-nhap/facebook') }}" class="btn-social btn-facebook">
                    Facebook
                </a>
            </div>

            <div class="register-section">
                <p class="register-text">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="register-link">Đăng ký ngay</a>
                </p>
            </div>
        </div>
    </div>
</div>



<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: var(--font-body);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        overflow-x: hidden;
    }

    .fade-out {
        opacity: 0;
        transition: opacity 1s ease;
    }

    .login-page {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 50px auto 0 auto;
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
        url("{{ asset('images/bg-login.jpg') }}") !important;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }

    .login-form-section {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--space-md);
    }

    .login-container {
        padding: 20px;
        margin-bottom: 80px;
        width: 100%;
        max-width: 400px;
        background: rgba(255, 255, 255, 0.7);
    }

    /* Form Header */
    .form-header {
        text-align: center;
        margin-bottom: var(--space-xs);
    }

    .form-title {
        font-family: var(--font-ui);
        font-size: var(--type-h2);
        font-weight: 400;
        color: var(--color-primary);
        margin: 0 0 var(--space-xs);
        letter-spacing: 1px;
    }

    .form-subtitle {
        color: var(--color-primary);
        margin: 0;
        font-size: var(--type-small);
    }

    /* Alerts */
    .alert {
        padding: var(--space-sm) var(--space-md);
        margin-bottom: var(--space-md);
        border: 2px solid;
        background: var(--color-white);
        position: relative;
        display: flex;
        align-items: center;
        gap: var(--space-sm);
    }

    .alert-success {
        border-color: var(--color-success);
        color: var(--color-success);
    }

    .alert-warning {
        border-color: var(--color-warning);
        color: var(--color-warning);
    }

    .alert-danger {
        border-color: var(--color-error);
        color: var(--color-error);
    }

    .alert-content {
        flex: 1;
        font-weight: 500;
    }

    .alert-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: var(--space-xs);
        font-size: 1.2rem;
        transition: var(--transition);
    }

    .alert-close:hover {
        opacity: 0.7;
    }

    .alert ul {
        margin: 0;
        padding-left: var(--space-md);
    }

    .alert li {
        margin-bottom: var(--space-xs);
    }

    /* Form Elements */
    .form-group {
        border-radius: 0px;
        margin-bottom: var(--space-sm);
    }

    .form-label {
        display: block;
        font-family: var(--font-ui);
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: var(--space-xs);
        font-size: var(--type-caption);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-control {
        border: 2px solid var(--color-light) !important;
        border-radius: 0px !important;
    }

    .form-control:focus {
        outline: none !important;
        border-color: var(--color-accent) !important;
        box-shadow: var(--color-accent) !important;

    }

    /* 
    .form-control {
        width: 100%;
        padding: var(--space-sm);
        border: 2px solid var(--color-light);
        border-radius: var(--border-radius);
        font-family: var(--font-body);
        font-size: var(--type-small);
        color: var(--color-dark);
        background: var(--color-white);
        transition: var(--transition);
        height: 44px;
    } */

    .form-control:focus {
        outline: none;
        border-color: var(--color-accent);
        box-shadow: var(--color-success);
    }

    .form-control.is-invalid {
        border-color: var(--color-error);
    }

    .form-control::placeholder {
        color: var(--color-muted);
        font-size: var(--type-caption);
    }

    /* Input Groups */
    .input-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: var(--space-sm);
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        user-select: none;
        font-size: 1rem;
        transition: var(--transition);
    }

    .password-toggle:hover {
        opacity: 0.7;
    }

    /* Validation */
    .invalid-feedback {
        display: block;
        margin-top: var(--space-xs);
        font-size: var(--type-caption);
        color: var(--color-error);
        font-weight: 500;
    }

    /* Form Options */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--space-md);
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: var(--space-xs);
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        border: 2px solid var(--color-light);
        border-radius: var(--border-radius);
        background: var(--color-white);
        cursor: pointer;
        margin-top: 0px;
    }


    .form-check-input:focus {
        outline: none;
        box-shadow: none;
    }

    .form-check-input:checked {
        background: var(--color-accent);
        border-color: var(--color-accent);
    }

    .form-check-label {
        font-size: var(--type-caption);
        color: var(--color-dark);
        cursor: pointer;
    }

    .forgot-link {
        font-size: var(--type-caption);
        color: var(--color-accent);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .forgot-link:hover {
        color: var(--color-accent);
        text-decoration: underline;
    }

    .btn-login {
        width: 100%;
        background: var(--color-accent);
        color: var(--color-white);
        border: 2px solid var(--color-accent);
        border-radius: var(--border-radius);
        font-family: var(--font-ui);
        font-size: var(--type-small);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: var(--transition);
        height: 48px;
        position: relative;
        overflow: hidden;
    }

    .btn-login:hover:not(:disabled) {
        background: var(--color-white);
        color: var(--color-accent);
        transition: background-color 0.3s ease-out, transform 0.3s ease-out;
    }

    .btn-login:disabled {
        opacity: 1;
        cursor: not-allowed;
        transform: none;
    }

    .btn-spinner {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: 0.15em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border .75s linear infinite;
        margin-right: 8px;
    }

    /* Định nghĩa chuyển động quay */
    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    /* Style cho nút khi bị disable (làm mờ đi) */
    .btn-login:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .divider {
        display: flex;
        align-items: center;
        margin: var(--space-xs) 0;
        gap: var(--space-md);
    }

    .divider::before,
    .divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--color-light);
    }

    .divider-text {
        font-size: var(--type-caption);
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Social Buttons */
    .social-buttons {
        display: flex;
        gap: var(--space-xs);
        margin-bottom: var(--space-xs);
    }

    .btn-social {
        flex: 1;
        padding: var(--space-xs);
        border: 2px solid var(--color-light);
        border-radius: var(--border-radius);
        background: var(--color-white);
        color: var(--color-dark);
        text-decoration: none;
        font-family: var(--font-ui);
        font-weight: 600;
        font-size: var(--type-caption);
        text-align: center;
        transition: var(--transition);
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-social:hover {
        transition: border-color 0.3s ease-out, transform 0.3s ease-out;
    }

    .btn-google:hover {
        border-color: #DB4437;
        color: #DB4437;
    }

    .btn-facebook:hover {
        border-color: #1877F2;
        color: #1877F2;
    }

    /* Register Link */
    .register-section {
        text-align: center;
        padding-top: var(--space-sm);
        border-top: 1px solid var(--color-light);
    }

    .register-text {
        font-size: var(--type-caption);
        color: var(--color-primary);
        margin: 0;
    }

    .register-link {
        color: var(--color-accent);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .register-link:hover {
        color: var(--color-accent);
        text-decoration: underline;
    }

    /* Animations */
    .fade-in {
        animation: fadeIn 0.8s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .fade-up {
        animation: fadeUp 0.8s ease forwards;
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-down {
        animation: fadeDown 0.8s ease forwards;
    }

    @keyframes fadeDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-left {
        animation: fadeLeft 0.8s ease forwards;
    }

    @keyframes fadeLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fade-right {
        animation: fadeRight 0.8s ease forwards;
    }

    @keyframes fadeRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

   

    .slide-right {
        animation: slideRight 0.8s ease;
    }

    @keyframes slideRight {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .login-page {
            margin: 50px auto;
            flex-direction: column;
            max-width: calc(100% - 10px);
        }

        .login-hero {
            flex: none;
            min-height: 10vh;
            padding: var(--space-md);
        }

        .hero-title {
            font-size: var(--type-h3);
        }

        .hero-features {
            display: none;
        }

        .login-form-section {
            padding: var(--space-sm);
        }

        .form-control {
            height: 48px;
            font-size: var(--type-small);
        }
    }

    @media (max-height: 700px) {
        .register-hero {
            padding: var(--space-sm);
        }

        .hero-features {
            display: none;
        }

        .form-group {
            margin-bottom: var(--space-xs);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Utilities */
    .d-none {
        display: none;
    }

    .d-flex {
        display: flex;
    }

    .w-100 {
        width: 100%;
    }

    .text-center {
        text-align: center;
    }
</style>
@endsection

@section('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById(fieldId + '-eye');
        if (field.type === 'password') {
            field.type = 'text';
            eye.innerHTML = "<i class='far fa-eye-slash'></i>";
        } else {
            field.type = 'password';
            eye.innerHTML = "<i class='far fa-eye'></i>";
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const btnLogin = document.getElementById("btnLogin");

        if (btnLogin) {
            const loginForm = btnLogin.closest("form");

            if (loginForm) {
                loginForm.addEventListener("submit", function(e) {
                    e.preventDefault();

                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }

                    if (btnLogin.disabled) return;

                    btnLogin.disabled = true;
                    const btnText = btnLogin.querySelector(".btn-text");
                    const btnLoading = btnLogin.querySelector(".btn-loading");

                    if (btnText) btnText.classList.add("d-none");
                    if (btnLoading) btnLoading.classList.remove("d-none");

                    setTimeout(function() {
                        loginForm.submit();
                    }, 2000);
                });
            }
        }

        window.addEventListener("pageshow", function(event) {
            if (btnLogin) {
                btnLogin.disabled = false;
                const btnText = btnLogin.querySelector(".btn-text");
                const btnLoading = btnLogin.querySelector(".btn-loading");

                if (btnText) btnText.classList.remove("d-none");
                if (btnLoading) btnLoading.classList.add("d-none");
            }
        });

        const alerts = document.querySelectorAll('.alert-auto');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 5000);
        });

        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                if (this.parentElement.parentElement.classList.contains('input-wrapper')) {
                    this.parentElement.parentElement.classList.add('focused');
                }
            });
            input.addEventListener('blur', function() {
                if (this.parentElement.parentElement.classList.contains('input-wrapper')) {
                    this.parentElement.parentElement.classList.remove('focused');
                }
            });
        });

        const link = document.createElement('link');
        link.href = 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&family=Source+Sans+Pro:wght@400;600&display=swap';
        link.rel = 'stylesheet';
        document.head.appendChild(link);
    });
</script>
@endsection 