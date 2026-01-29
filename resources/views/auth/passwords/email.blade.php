@extends('layouts.app')

@section('title', 'Quên mật khẩu')

@section('content')
<div class="forgot-password-wrapper">
     <div class="container px-0">
          <div class="row justify-content-center">
               <div class="col-md-6 col-lg-3">
                    <div class="forgot-password-card">

                         <div class="text-center mb-4">
                              <div class="fp-icon-wrapper mb-3">
                                   <i class="fas fa-unlock"></i>
                              </div>
                              <h2 class="fp-form-title">Quên mật khẩu?</h2>
                              <p class="fp-form-subtitle">Đừng lo lắng! Chúng tôi sẽ giúp bạn lấy lại</p>
                         </div>

                         @if (session('status'))
                         <div class="fp-custom-alert fp-alert-success">
                              <span>{{ session('status') }}</span>
                         </div>
                         @endif

                         @if ($errors->any())
                         <div class="fp-custom-alert fp-alert-danger">
                              <span>{{ $errors->first('email') }}</span>
                         </div>
                         @endif

                         <form method="POST" action="{{ route('password.email') }}" class="fp-forgot-password-form">
                              @csrf
                              <div class="fp-form-group">
                                   <label for="email" class="fp-form-label">
                                        Địa chỉ Email của bạn
                                   </label>
                                   <input
                                        id="email"
                                        type="email"
                                        class="fp-custom-input @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="Nhập email đã đăng ký"
                                        required
                                        autofocus>
                                   <small class="fp-form-text">Chúng tôi sẽ gửi đường dẫn đặt lại mật khẩu đến email này</small>
                              </div>

                              <button type="submit" class="fp-custom-btn">
                                   <span class="fp-btn-text">Gửi email đặt lại mật khẩu</span>
                              </button>
                         </form>

                         <div class="text-center mt-4">
                              <p class="fp-back-link-text">
                                   Đã nhớ ra mật khẩu?
                                   <a href="{{ route('login') }}" class="fp-back-link">Đăng nhập ngay</a>
                              </p>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function() {
          const form = document.querySelector('.fp-forgot-password-form');
          const submitBtn = document.querySelector('.fp-custom-btn');

          if (form && submitBtn) {
               form.addEventListener('submit', function() {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
               });
          }

          const input = document.querySelector('.fp-custom-input');
          if (input) {
               input.addEventListener('focus', function() {
                    this.parentNode.classList.add('focused');
               });

               input.addEventListener('blur', function() {
                    if (!this.value) {
                         this.parentNode.classList.remove('focused');
                    }
               });
          }
     });
     setTimeout(() => {
          document.querySelectorAll('.fp-custom-alert').forEach(alert => {
               alert.style.transition = 'opacity 1s ease';
               alert.style.opacity = '0';
               setTimeout(() => alert.remove(), 1000);
          });
     }, 4000);
</script>
@endsection

@push('styles')
<style>
     .forgot-password-wrapper {
          padding: var(--space-xl) 0;
          background: var(--color-light);
          min-height: 100vh;
          display: flex;
          justify-content: center;
          background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
          url("{{ asset('images/bg-login.jpg') }}") !important;
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
     }

     .forgot-password-card {
          background: var(--color-white);
          border: 1px solid var(--color-light);
          border-radius: var(--btn-radius);
          box-shadow: var(--shadow-light);
          padding: var(--space-lg);
          text-align: center;
     }

     .fp-icon-wrapper {
          width: 64px;
          height: 64px;
          border-radius: 50%;
          background: var(--color-accent);
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto;
          font-size: 1.5rem;
          color: var(--color-white);
          box-shadow: var(--shadow-light);
     }

     .fp-form-title {
          font-family: var(--font-ui);
          font-size: var(--type-h4);
          font-weight: 500;
          color: var(--color-primary);
          margin-bottom: var(--space-xs);
     }

     .fp-form-subtitle {
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-muted);
          margin: 0;
     }

     /* Alert Styles */
     .fp-custom-alert {
          padding: var(--space-xs) var(--space-sm);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          font-size: var(--type-small);
          margin-bottom: var(--space-sm);
          text-align: center;
     }

     .fp-alert-success {
          background: rgba(39, 174, 96, 0.1);
          color: var(--color-success);
          border-left: 3px solid var(--color-success);
     }

     .fp-alert-danger {
          background: rgba(231, 76, 60, 0.1);
          color: var(--color-error);
          border-left: 3px solid var(--color-error);
     }

     /* Form */
     .fp-form-group {
          margin-bottom: var(--space-md);
          text-align: left;
     }

     .fp-form-label {
          display: block;
          font-family: var(--font-ui);
          font-size: var(--type-caption);
          font-weight: 600;
          color: var(--color-primary);
          margin-bottom: 4px;
     }

     .fp-custom-input {
          width: 100%;
          padding: var(--space-xs);
          border: 2px solid var(--color-light);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-dark);
          background: var(--color-white);
          transition: var(--btn-transition);
     }

     .fp-custom-input:focus {
          outline: none;
          border-color: var(--color-accent);
          box-shadow: var(--color-accent);
     }

     .fp-custom-input.is-invalid {
          border-color: var(--color-error);
          box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.16);
     }

     .fp-form-text {
          font-size: 11px;
          font-family: var(--font-body);
          color: var(--color-muted);
          margin-top: 2px;
          display: block;
          line-height: 1.4;
     }

     /* Button */
     .fp-custom-btn {
          width: 100%;
          background: var(--color-accent);
          color: var(--color-white);
          border: 2px solid var(--color-accent);
          padding: var(--space-sm) var(--space-lg);
          border-radius: var(--btn-radius);
          font-family: var(--font-ui);
          font-size: var(--type-small);
          font-weight: 500;
          cursor: pointer;
          transition: var(--btn-transition);
          height: 42px;
          display: flex;
          align-items: center;
          justify-content: center;
     }

     .fp-custom-btn:hover:not(:disabled) {
          background: var(--color-white);
          color: var(--color-accent);
     }

     .fp-custom-btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          background: var(--color-light);
          border-color: var(--color-light);
          color: var(--color-muted);
     }

     .fp-custom-btn.loading::after {
          content: "";
          width: 16px;
          height: 16px;
          border: 2px solid var(--color-white);
          border-top-color: transparent;
          border-radius: 50%;
          margin-left: var(--space-xs);
          display: inline-block;
          animation: spin 0.8s linear infinite;
     }

     /* Back Link */
     .fp-back-link-text {
          font-size: var(--type-small);
          font-family: var(--font-body);
          color: var(--color-muted);
     }

     .fp-back-link {
          color: var(--color-accent);
          text-decoration: none;
          font-weight: 600;
          transition: var(--transition);
     }

     .fp-back-link:hover {
          color: var(--color-accent);
          text-decoration: underline;
     }

     /* Animations */
     @keyframes spin {
          to {
               transform: rotate(360deg);
          }
     }

     /* Responsive */
     @media (max-width: 576px) {
          .forgot-password-card {
               padding: var(--space-md);
          }

          .fp-form-title {
               font-size: var(--type-h5);
          }
     }
</style>
@endpush