@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu')

@section('content')
<div class="reset-password-wrapper">
     <div class="container">
          <div class="reset-password-card">
               <div class="text-center mb-4 rp-fade-in">
                    <h4 class="rp-form-title">Đặt lại mật khẩu</h4>
                    <p class="rp-form-subtitle">Nhập mật khẩu mới cho tài khoản</p>
               </div>

               @if ($errors->any())
               <div class="rp-custom-alert rp-alert-danger">
                    <ul class="mb-0">
                         @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                         @endforeach
                    </ul>
               </div>
               @endif

               <form method="POST" action="{{ route('password.update') }}" class="rp-reset-password-form">
                    @csrf

                    <!-- token từ URL -->
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ request('email') }}">

                    <div class="rp-input-group-animated">
                         <label for="password" class="rp-form-label">Mật khẩu mới</label>
                         <div class="rp-input-wrapper">
                              <input
                                   id="password"
                                   type="password"
                                   class="rp-form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="Nhập mật khẩu mới"
                                   required
                                   autocomplete="new-password">
                              <div class="rp-input-border"></div>
                         </div>
                         <div class="rp-password-strength" id="passwordStrength" style="display: none;">
                              <div class="rp-strength-bar">
                                   <div class="rp-strength-fill" id="strengthFill"></div>
                              </div>
                              <span class="rp-strength-text" id="strengthText">Yếu</span>
                         </div>
                         @error('password')
                         <div class="rp-invalid-feedback">{{ $message }}</div>
                         @enderror
                    </div>

                    <div class="rp-input-group-animated">
                         <label for="password-confirm" class="rp-form-label">Xác nhận mật khẩu</label>
                         <div class="rp-input-wrapper">
                              <input
                                   id="password-confirm"
                                   type="password"
                                   class="rp-form-control"
                                   name="password_confirmation"
                                   placeholder="Nhập lại mật khẩu"
                                   required
                                   autocomplete="new-password">
                              <div class="rp-input-border"></div>
                         </div>
                         <div class="rp-password-match" id="passwordMatch" style="display: none; margin-top: 0.5rem; font-size: 0.875rem;"></div>
                    </div>

                    <div class="d-grid">
                         <button type="submit" class="rp-custom-btn">
                              Đặt lại mật khẩu
                         </button>
                    </div>
               </form>
          </div>
     </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function() {
          const form = document.querySelector('.rp-reset-password-form');
          const submitBtn = document.querySelector('.rp-custom-btn');
          const passwordInput = document.getElementById('password');
          const confirmInput = document.getElementById('password-confirm');
          const strengthIndicator = document.getElementById('passwordStrength');
          const strengthFill = document.getElementById('strengthFill');
          const strengthText = document.getElementById('strengthText');
          const matchIndicator = document.getElementById('passwordMatch');

          function checkPasswordStrength(password) {
               let strength = 0;
               let feedback = [];

               if (password.length >= 8) strength += 1;
               else feedback.push('ít nhất 8 ký tự');

               if (/[A-Z]/.test(password)) strength += 1;
               else feedback.push('chữ hoa');

               if (/[a-z]/.test(password)) strength += 1;
               else feedback.push('chữ thường');

               if (/[0-9]/.test(password)) strength += 1;
               else feedback.push('số');

               if (/[^A-Za-z0-9]/.test(password)) strength += 1;
               else feedback.push('ký tự đặc biệt');

               return {
                    strength,
                    feedback
               };
          }

          function updateStrengthDisplay(strength) {
               const strengthPercent = (strength / 5) * 100;
               const strengthLabels = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
               const strengthColors = [
                    'linear-gradient(135deg, #ff4757, #ff3838)',
                    'linear-gradient(135deg, #ff6b6b, #ee5a52)',
                    'linear-gradient(135deg, #feca57, #ff9ff3)',
                    'linear-gradient(135deg, #48dbfb, #0abde3)',
                    'linear-gradient(135deg, #1dd1a1, #10ac84)'
               ];

               strengthFill.style.width = strengthPercent + '%';
               strengthFill.style.background = strengthColors[strength - 1] || strengthColors[0];
               strengthText.textContent = strengthLabels[strength - 1] || strengthLabels[0];
               strengthText.style.color = strength >= 3 ? '#10ac84' : '#ff4757';
          }

          if (passwordInput) {
               passwordInput.addEventListener('input', function() {
                    const password = this.value;

                    if (password.length > 0) {
                         strengthIndicator.style.display = 'flex';
                         const {
                              strength
                         } = checkPasswordStrength(password);
                         updateStrengthDisplay(strength);
                    } else {
                         strengthIndicator.style.display = 'none';
                    }

                    if (confirmInput.value) {
                         checkPasswordMatch();
                    }
               });
          }

          function checkPasswordMatch() {
               const password = passwordInput.value;
               const confirm = confirmInput.value;

               if (confirm.length > 0) {
                    matchIndicator.style.display = 'block';
                    if (password === confirm) {
                         matchIndicator.textContent = '✓ Mật khẩu khớp';
                         matchIndicator.style.color = '#10ac84';
                    } else {
                         matchIndicator.textContent = '✗ Mật khẩu không khớp';
                         matchIndicator.style.color = '#ff4757';
                    }
               } else {
                    matchIndicator.style.display = 'none';
               }
          }

          if (confirmInput) {
               confirmInput.addEventListener('input', checkPasswordMatch);
          }

          if (form && submitBtn) {
               form.addEventListener('submit', function(e) {
                    const password = passwordInput.value;
                    const confirm = confirmInput.value;

                    if (password !== confirm) {
                         e.preventDefault();
                         confirmInput.classList.add('is-invalid');
                         return false;
                    }

                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Đang xử lý...';
               });
          }

          const inputs = document.querySelectorAll('.rp-form-control');
          inputs.forEach(input => {
               input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
               });
          });


          inputs.forEach(input => {
               input.addEventListener('focus', function() {
                    this.parentNode.parentNode.classList.add('focused');
               });

               input.addEventListener('blur', function() {
                    if (!this.value) {
                         this.parentNode.parentNode.classList.remove('focused');
                    }
               });
          });
     });
</script>
@endsection

@push('styles')
<style>
     /* Reset Password Page Styles - Green Vibes Theme */
     .reset-password-wrapper {
          padding: var(--space-xl) 0;
          background: var(--color-light);
          min-height: 100vh;
          display: flex;
          align-items: center;
     }

     .reset-password-card {
          background: var(--color-white);
          border: 1px solid var(--color-light);
          border-radius: var(--btn-radius);
          box-shadow: var(--shadow-light);
          padding: var(--space-lg);
          max-width: 480px;
          margin: 0 auto;
     }

     .rp-form-title {
          font-family: var(--font-ui);
          font-size: var(--type-h4);
          font-weight: 500;
          color: var(--color-primary);
          margin-bottom: var(--space-xs);
     }

     .rp-form-subtitle {
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-muted);
          margin: 0;
     }

     /* Alerts */
     .rp-custom-alert {
          padding: var(--space-xs) var(--space-sm);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          font-size: var(--type-small);
          margin-bottom: var(--space-sm);
     }

     .rp-alert-danger {
          background: rgba(231, 76, 60, 0.1);
          color: var(--color-error);
          border-left: 3px solid var(--color-error);
     }

     /* Input Groups */
     .rp-input-group-animated {
          margin-bottom: var(--space-md);
     }

     .rp-form-label {
          display: block;
          font-family: var(--font-ui);
          font-size: var(--type-caption);
          font-weight: 600;
          color: var(--color-primary);
          margin-bottom: 4px;
     }

     .rp-input-wrapper {
          position: relative;
          display: flex;
          flex-direction: column;
     }

     .rp-form-control {
          width: 100%;
          padding: var(--space-sm);
          border: 2px solid var(--color-light);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-dark);
          background: var(--color-white);
          transition: var(--btn-transition);
     }

     .rp-form-control:focus {
          outline: none;
          border-color: var(--color-accent);
          box-shadow: 0 0 0 0.2rem rgba(243, 156, 18, 0.16);
     }

     .rp-form-control.is-invalid {
          border-color: var(--color-error);
          box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.16);
     }

     .rp-input-border {
          position: absolute;
          left: 0;
          right: 0;
          bottom: 0;
          height: 2px;
          background: var(--color-accent);
          transform: scaleX(0);
          transition: transform 0.3s ease;
          border-radius: var(--btn-radius);
     }

     .rp-form-control:focus+.rp-input-border {
          transform: scaleX(1);
     }

     .rp-invalid-feedback {
          font-size: 11px;
          font-family: var(--font-body);
          color: var(--color-error);
          margin-top: 2px;
          display: block;
     }

     /* Password Strength Indicator */
     .rp-password-strength {
          margin-top: var(--space-xs);
     }

     .rp-strength-bar {
          background: var(--color-light);
          border-radius: var(--btn-radius);
          overflow: hidden;
          height: 6px;
          margin-bottom: 4px;
     }

     .rp-strength-fill {
          height: 100%;
          width: 0;
          background: var(--color-error);
          transition: width 0.3s ease, background 0.3s ease;
     }

     .rp-strength-text {
          font-size: 11px;
          font-family: var(--font-body);
          color: var(--color-muted);
     }

     /* Password Match */
     .rp-password-match {
          font-family: var(--font-body);
          font-size: 11px;
          font-weight: 500;
          color: var(--color-muted);
     }

     /* Button */
     .rp-custom-btn {
          display: flex;
          justify-content: center;
          align-items: center;

          width: 100%;
          background: var(--color-accent);
          color: var(--color-dark);
          border: none;
          border-radius: var(--btn-radius);
          padding: 10px 18px;
          font-size: var(--type-small);
          font-weight: 500;
          cursor: pointer;
          transition: var(--btn-transition);
     }

     .rp-custom-btn:hover:not(:disabled) {
          background: var(--color-accent);
          color: var(--color-white);
     }

     .rp-custom-btn:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          background: var(--color-light);
          color: var(--color-muted);
          border-color: var(--color-light);
     }

     /* Animation */
     .rp-fade-in {
          animation: fadeIn 0.6s ease forwards;
     }

     @keyframes fadeIn {
          from {
               opacity: 0;
               transform: translateY(-10px);
          }

          to {
               opacity: 1;
               transform: translateY(0);
          }
     }

     /* Responsive */
     @media (max-width: 576px) {
          .reset-password-card {
               padding: var(--space-md);
          }

          .rp-form-title {
               font-size: var(--type-h5);
          }
     }
</style>
@endpush