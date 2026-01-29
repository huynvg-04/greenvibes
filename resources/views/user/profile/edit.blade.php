@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="profile-container">
     <div class="profile-header">
          <h1 class="profile-title">Hồ sơ cá nhân</h1>
     </div>

     <div class="profile-body">
          @if(session('success'))
          <div class="alert-modern alert-success alert-auto">
               <i class="fas fa-check-circle alert-icon"></i>
               <p class="alert-text">{{ session('success') }}</p>
               <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
               </button>
          </div>
          @endif

          @if(session('error'))
          <div class="alert-modern alert-error alert-auto">
               <i class="fas fa-exclamation-circle alert-icon"></i>
               <p class="alert-text">{{ session('error') }}</p>
               <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
               </button>
          </div>
          @endif

          <form action="{{ route('user.profile.update') }}" method="POST" id="profileForm">
               @csrf
               @method('PUT')
               <div class="profile-content">
                    <!-- Cột trái -->
                    <div class="form-section">
                         <div class="section-divider">
                              Thông tin cơ bản
                         </div>
                         <div class="row my-0">

                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label for="name" class="form-label-custom">Tên hiển thị</label>
                                        <input type="text"
                                             name="name"
                                             id="name"
                                             class="form-input-custom @error('name') error @enderror"
                                             value="{{ old('name', $user->name) }}"
                                             required>
                                        <div class="helper-text">Tên này sẽ hiển thị trên tài khoản của bạn</div>
                                        @error('name')
                                        <span class="error-message">{{ $message }}</span>
                                        @enderror
                                   </div>
                              </div>

                              <div class="col-md-6">
                                   <div class="form-group">
                                        <label class="form-label-custom">Hạng thành viên</label>

                                        <div class="input-group">
                                             <input type="text"
                                                  class="form-input-custom bg-light fw-bold text-uppercase"
                                                  value="{{ $user->customerProfile->tier->name ?? 'Thành viên mới' }}"
                                                  disabled
                                                  readonly>
                                        </div>

                                        <div class="helper-text text-muted">
                                             Hạng được cập nhật tự động dựa trên chi tiêu.
                                        </div>
                                   </div>
                              </div>

                         </div>
                         <div class="form-group">
                              <label for="gender" class="form-label-custom">Giới tính</label>
                              <select name="gender" id="gender" class="form-input-custom gender-input @error('gender') error @enderror">
                                   <option value="male" {{ (old('gender', $user->customerProfile->gender ?? '') == 'male') ? 'selected' : '' }}>Nam</option>
                                   <option value="female" {{ (old('gender', $user->customerProfile->gender ?? '') == 'female') ? 'selected' : '' }}>Nữ</option>
                                   <option value="other" {{ (old('gender', $user->customerProfile->gender ?? '') == 'other') ? 'selected' : '' }}>Khác</option>
                              </select>
                              <div class="helper-text"> </div>
                              @error('gender')
                              <span class="error-message">{{ $message }}</span>
                              @enderror
                         </div>
                         <div class="form-group">
                              <label for="phone" class="form-label-custom">Số điện thoại</label>
                              <input type="text"
                                   name="phone"
                                   id="phone"
                                   class="form-input-custom @error('phone') error @enderror"
                                   value="{{ old('phone', $user->customerProfile->phone) }}"
                                   required>
                              <div class="helper-text">Nhân viên giao hàng sẽ liên hệ với bạn qua số điện thoại này</div>
                              @error('phone')
                              <span class="error-message">{{ $message }}</span>
                              @enderror
                         </div>

                         <div class="form-group">
                              <label class="form-label-custom">Địa chỉ giao hàng</label>

                              <input type="hidden" name="address" id="hidden_address"
                                   value="{{ old('address', $user->customerProfile->address) }}">

                              @if($user->customerProfile->address)
                              <div class="mb-2 p-2 bg-light border small text-muted">
                                   <i class="fas fa-map-marker-alt me-1"></i>
                                   <strong>{{ $user->customerProfile->address }}</strong>
                              </div>
                              @endif

                              <div class="address-selector mt-2">
                                   <div class="row g-2 mb-2">
                                        <div class="col-md-6">
                                             <select class="province-input form-input-custom" id="province" title="Chọn Tỉnh Thành">
                                                  <option value="0">Tỉnh/Thành phố</option>
                                             </select>
                                        </div>
                                        <div class="col-md-6">
                                             <select class="form-input-custom ward-input" id="ward" title="Chọn Phường/Xã">
                                                  <option value="0">Phường/Xã</option>
                                             </select>
                                        </div>
                                   </div>
                                   <input type="text" id="specific_address" class="form-input-custom"
                                        placeholder="Số nhà, tên đường, tòa nhà... (Nhập để thay đổi địa chỉ)">
                              </div>

                              <div class="helper-text">Chọn Tỉnh/Thành và Phường/Xã để cập nhật địa chỉ chính xác.</div>
                              @error('address')
                              <span class="error-message">{{ $message }}</span>
                              @enderror
                         </div>


                         <div class="form-group">
                              @if($user->customerProfile && $user->customerProfile->facebook_id)
                              <label class="form-label-custom">Phương thức đăng nhập</label>
                              <div style="position: relative;">
                                   <input type="text"
                                        class="form-input-custom"
                                        value="Facebook"
                                        disabled
                                        readonly
                                        style="color: #1877F2; font-weight: bold; padding-left: 40px;">
                                   <i class="fab fa-facebook" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #1877F2;"></i>
                              </div>
                              <div class="helper-text">Bạn đang đăng nhập thông qua tài khoản Facebook liên kết.</div>

                              @elseif($user->customerProfile && $user->customerProfile->google_id)
                              <label class="form-label-custom">Phương thức đăng nhập</label>
                              <div style="position: relative;">
                                   <input type="text"
                                        class="form-input-custom"
                                        value="Google"
                                        disabled
                                        readonly
                                        style="color: #DB4437; font-weight: bold; padding-left: 40px;">
                                   <i class="fab fa-google" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #DB4437;"></i>
                              </div>
                              <div class="helper-text">Bạn đang đăng nhập thông qua tài khoản Google liên kết.</div>

                              @else
                              <label for="email" class="form-label-custom">Địa chỉ Email</label>
                              <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-input-custom"
                                   value="{{ $user->email }}"
                                   readonly>
                              <div class="helper-text">Email dùng để đăng nhập và nhận thông báo (không thể chỉnh sửa)</div>
                              @endif
                         </div>
                    </div>

                    <!-- Cột phải -->
                    <div class="password-wrapper">
                         <div class="section-divider">
                              Đổi mật khẩu (tùy chọn)
                         </div>

                         <div class="password-section">
                              <div class="form-group">
                                   <label for="current_password" class="form-label-custom">Mật khẩu hiện tại</label>
                                   <input type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="form-input-custom @error('current_password') error @enderror" autocomplete="new-password"
                                        autocorrect="off"
                                        spellcheck="off">
                                   <div class="helper-text">Nhập mật khẩu hiện tại để xác minh</div>
                                   @error('current_password')
                                   <span class="error-message">{{ $message }}</span>
                                   @enderror
                              </div>

                              <div class="form-group">
                                   <label for="new_password" class="form-label-custom">Mật khẩu mới</label>
                                   <input type="password"
                                        name="new_password"
                                        id="new_password"
                                        class="form-input-custom @error('new_password') error @enderror">
                                   <div class="helper-text">Tối thiểu 8 ký tự, bao gồm chữ và số</div>
                                   @error('new_password')
                                   <span class="error-message">{{ $message }}</span>
                                   @enderror
                              </div>

                              <div class="form-group">
                                   <label for="new_password_confirmation" class="form-label-custom">Xác nhận mật khẩu mới</label>
                                   <input type="password"
                                        name="new_password_confirmation"
                                        id="new_password_confirmation"
                                        class="form-input-custom">
                                   <div class="helper-text">Nhập lại mật khẩu mới để xác nhận</div>
                              </div>
                         </div>
                    </div>
               </div>

               <button type="submit" class="submit-button" id="submitBtn">
                    Cập nhật hồ sơ
               </button>
          </form>
     </div>
</div>

<script>
     document.addEventListener('DOMContentLoaded', function() {
          const provinceSelect = document.getElementById('province');
          const wardSelect = document.getElementById('ward');
          const specificAddress = document.getElementById('specific_address');
          const hiddenAddressInput = document.getElementById('hidden_address');

          let provincesData = [];

          function updateHiddenAddress() {
               const p = provinceSelect.options[provinceSelect.selectedIndex]?.text;
               const w = wardSelect.options[wardSelect.selectedIndex]?.text;
               const s = specificAddress.value.trim();

               if (
                    p && w && s &&
                    p !== 'Tỉnh/Thành phố' &&
                    w !== 'Phường/Xã'
               ) {
                    hiddenAddressInput.value = `${s}, ${w}, ${p}`;
               }

          }

          fetch('https://esgoo.net/api-tinhthanh-new/4/0.htm')
               .then(res => res.json())
               .then(json => {
                    if (json.error === 0) {
                         provincesData = json.data;
                         provincesData.forEach(province => {
                              provinceSelect.add(
                                   new Option(province.full_name, province.id)
                              );
                         });
                    }
               });

          provinceSelect.addEventListener('change', function() {
               wardSelect.length = 1;

               const provinceId = this.value;
               if (!provinceId || provinceId == 0) return;

               const province = provincesData.find(p => p.id === provinceId);
               if (!province || !province.data2) return;

               province.data2.forEach(ward => {
                    wardSelect.add(
                         new Option(ward.full_name, ward.id)
                    );
               });

          });

          wardSelect.addEventListener('change', updateHiddenAddress);
          specificAddress.addEventListener('input', updateHiddenAddress);
     });

     document.addEventListener('DOMContentLoaded', function() {
          const form = document.getElementById('profileForm');
          const submitBtn = document.getElementById('submitBtn');
          const originalText = submitBtn.textContent;

          const inputs = form.querySelectorAll('.form-input-custom');
          inputs.forEach(input => {
               input.addEventListener('input', function() {
                    if (this.classList.contains('error')) {
                         this.classList.remove('error');
                         const errorMsg = this.parentNode.querySelector('.error-message');
                         if (errorMsg) {
                              errorMsg.style.display = 'none';
                         }
                    }
               });
          });

          form.addEventListener('submit', function(e) {
               submitBtn.textContent = 'Đang cập nhật...';
               submitBtn.classList.add('loading');
               submitBtn.disabled = true;

               setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.classList.remove('loading');
                    submitBtn.disabled = false;
               }, 5000);
          });

          const newPassword = document.getElementById('new_password');
          const confirmPassword = document.getElementById('new_password_confirmation');

          function validatePasswordMatch() {
               if (confirmPassword.value && newPassword.value !== confirmPassword.value) {
                    confirmPassword.classList.add('error');
                    let errorMsg = confirmPassword.parentNode.querySelector('.error-message');
                    if (!errorMsg) {
                         errorMsg = document.createElement('span');
                         errorMsg.className = 'error-message';
                         confirmPassword.parentNode.appendChild(errorMsg);
                    }
                    errorMsg.textContent = 'Mật khẩu xác nhận không khớp';
                    errorMsg.style.display = 'block';
               } else {
                    confirmPassword.classList.remove('error');
                    const errorMsg = confirmPassword.parentNode.querySelector('.error-message');
                    if (errorMsg) {
                         errorMsg.style.display = 'none';
                    }
               }
          }

          confirmPassword.addEventListener('input', validatePasswordMatch);
          newPassword.addEventListener('input', validatePasswordMatch);
     });
     document.addEventListener('DOMContentLoaded', function() {
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
     });
</script>
<style>
     .profile-container {
          max-width: 100%;
          margin: 0 auto;
     }

     .profile-card {
          background: var(--color-white);
          overflow: hidden;
          max-height: 85vh;
          display: flex;
          flex-direction: column;
     }

     .profile-header {
          height: 140px;
          background: #f4f4f4;
          padding: var(--space-xl) 0 var(--space-xl) 60px;
          margin-top: 50px;
          font-family: var(--font-body);
          display: flex;
          align-items: center;
     }

     .profile-title {
          font-family: var(--font-ui);
          font-size: var(--type-h2);
          font-weight: 300;
          color: var(--color-primary);
          margin: 0;
          letter-spacing: 0.5px;
     }

     .profile-subtitle {
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-muted);
          margin: 0;
     }

     .profile-body {
          padding: var(--space-md);
          flex: 1;
          overflow-y: auto;
     }

     /* Alert System */
     .alert-modern {
          display: flex;
          align-items: center;
          padding: var(--space-xs) var(--space-sm);
          margin-bottom: var(--space-sm);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          position: relative;
          border: none;
          font-size: var(--type-small);
     }

     .alert-success {
          background: rgba(39, 174, 96, 0.1);
          color: var(--color-success);
          border-left: 3px solid var(--color-success);
     }

     .alert-error {
          background: rgba(231, 76, 60, 0.1);
          color: var(--color-error);
          border-left: 3px solid var(--color-error);
     }

     .alert-icon {
          margin-right: var(--space-xs);
          font-size: 1rem;
     }

     .alert-text {
          flex: 1;
          margin: 0;
          font-weight: 400;
     }

     .alert-close {
          background: none;
          border: none;
          color: inherit;
          font-size: 1rem;
          cursor: pointer;
          padding: var(--space-xs);
          margin-left: var(--space-xs);
          transition: var(--btn-transition);
          border-radius: var(--btn-radius);
     }

     /* Profile Content: 2 Columns */
     .profile-content {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: var(--space-lg);
          margin: 0 100px 0 100px;
     }

     /* Left Column - Basic Info */
     .form-section {
          display: flex;
          flex-direction: column;
          gap: var(--space-sm);
     }

     /* Right Column - Password */
     .password-wrapper {
          display: flex;
          flex-direction: column;
          gap: var(--space-sm);
     }

     .section-divider {
          font-family: var(--font-ui);
          font-size: var(--type-caption);
          font-weight: 600;
          color: var(--color-primary);
          text-transform: uppercase;
          letter-spacing: 0.5px;
          padding: var(--space-xs) 0;
          border-bottom: 2px solid var(--color-light);
          margin-bottom: var(--space-xs);
     }

     .password-section {
          display: flex;
          flex-direction: column;
          gap: var(--space-sm);
     }

     .password-section-title {
          font-family: var(--font-ui);
          font-size: var(--type-small);
          font-weight: 500;
          color: var(--color-primary);
          margin-bottom: var(--space-xs);
     }

     /* Form Groups */
     .form-group {
          margin-bottom: 0;
     }

     .form-label-custom {
          display: block;
          font-family: var(--font-ui);
          font-size: var(--type-caption);
          font-weight: 600;
          color: var(--color-primary);
          margin-bottom: 2px;
     }

     .form-input-custom {
          width: 100%;
          padding: var(--space-xs) var(--space-sm);
          border: 2px solid var(--color-light);
          border-radius: var(--btn-radius);
          font-family: var(--font-body);
          font-size: var(--type-small);
          color: var(--color-dark);
          background: var(--color-white);
          transition: var(--btn-transition);
          height: 36px;
     }

     .form-input-custom:focus {
          outline: none;
          border-color: var(--color-accent);
          box-shadow: var(--color-success);
     }

     .form-input-custom.error {
          border-color: var(--color-error);
          box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.16);
     }

     select.form-input-custom {
          background-color: #fff;
          color: #111827;
          opacity: 1;
          cursor: pointer;
     }


     .gender-input {
          cursor: pointer !important;
     }

     .province-input {
          cursor: pointer !important;
     }

     .ward-input {
          cursor: pointer !important;
     }

     .helper-text {
          font-family: var(--font-body);
          font-size: 11px;
          color: var(--color-muted);
          margin-top: 2px;
          line-height: 1.3;
     }

     .error-message {
          font-family: var(--font-body);
          font-size: 11px;
          color: var(--color-error);
          margin-top: 2px;
          display: block;
          font-weight: 500;
     }


     .submit-button {
          background: var(--color-accent);
          color: var(--color-white);
          border: 2px solid var(--color-accent);
          font-family: var(--font-ui);
          font-size: var(--type-small);
          font-weight: 500;
          cursor: pointer;
          transition: var(--btn-transition);
          min-width: 180px;
          margin-top: var(--space-md);
          height: 40px;
          display: block;
          margin-left: auto;
          margin-right: auto;
     }

     .submit-button:hover:not(:disabled) {
          background: transparent;
          color: var(--color-accent);
     }

     .submit-button:disabled {
          opacity: 0.6;
          cursor: not-allowed;
          background: var(--color-light);
          color: var(--color-muted);
          border-color: var(--color-accent);
     }

     /* Responsive */
     @media (max-width: 768px) {
          .profile-content {
               grid-template-columns: 1fr;
          }

          .submit-button {
               width: 100%;
          }
     }
</style>

@endsection