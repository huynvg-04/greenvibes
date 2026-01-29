<div id="notification-container">
    @if (session('success'))
    <div class="notification-toast bg-opacity-25 bg-card success">
        <div class="notification-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5" />
            </svg>
        </div>
        <div class="notification-content text-body">
            <h4 class="notification-title text-body">Thành công</h4>
            <p class="notification-message text-body">{{ session('success') }}</p>
        </div>
        <button class="close-btn" onclick="closeToast(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg></button>
        <div class="progress-bar"></div>
    </div>
    @endif

    @if (session('error'))
    <div class="notification-toast error ">
        <div class="notification-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M20 4L4 20M4 4l16 16" />
            </svg>
        </div>
        <div class="notification-content">
            <h4 class="notification-title text-body">Lỗi</h4>
            <p class="notification-message text-body">{{ session('error') }}</p>
        </div>
        <button class="close-btn" onclick="closeToast(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg></button>
        <div class="progress-bar"></div>
    </div>
    @endif

    @if ($errors->any())
    <div class="notification-toast error bg-card">
        <div class="notification-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <path d="M15 9l-6 6M9 9l6 6" />
            </svg>

        </div>
        <div class="notification-content">
            <h4 class="notification-title text-body">Lỗi!</h4>
            <div class="notification-message text-body">
                <ul style="margin: 0; padding-left: 15px;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button class="close-btn" onclick="closeToast(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12" />
            </svg></button>
        <div class="progress-bar"></div>
    </div>
    @endif
</div>
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/admin.css') }}">
@endpush