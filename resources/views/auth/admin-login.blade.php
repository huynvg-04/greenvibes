<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/admin-login.css') }} ">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm border-0 rounded-4 login-card mx-auto"
            style="width: 500px; max-width: 100%; background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(0px);">
            <div class="card-body p-6 p-md-7">

                <div class="text-center mb-4">
                    <h4 class="fw-bold text-dark">Đăng nhập Quản trị</h4>
                    <p class="text-muted small">Dành cho quản lý và nhân viên</p>
                </div>

                <form method="POST" action="{{ route('admin.login') }}">

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 alert-auto mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                    </div>
                    @endif

                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 alert-auto mb-3" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
                    </div>
                    @endif


                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email</label>
                        <input type="email" name="email" class="form-control form-control-lg rounded-3" placeholder="Nhập email" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Mật khẩu</label>
                        <input type="password" name="password" class="form-control form-control-lg rounded-3" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="form-group mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_KEY') }}"></div>
                        @error('g-recaptcha-response')
                        <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-danger btn-lg rounded-3 fw-semibold" id="btnLogin">
                            Đăng nhập
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.querySelector("form");
            const btn = document.getElementById("btnLogin");
            const originalText = btn.textContent;

            form.addEventListener("submit", function() {
                btn.disabled = true;
                btn.textContent = "Đang đăng nhập...";
            });

            window.addEventListener("pageshow", function() {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                document.querySelectorAll('.alert-auto').forEach(function(el) {
                    el.classList.remove("show");
                    el.classList.add("fade");
                    setTimeout(() => el.remove(), 500);
                });
            }, 4000);
        });
    </script>
</body>

</html>