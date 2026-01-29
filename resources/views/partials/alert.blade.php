@if(session('success'))
<div class="alert-modern alert-success alert-auto" role="alert">
    <i class="fas fa-check-circle alert-icon" aria-hidden="true"></i>
    <p class="alert-text">{{ session('success') }}</p>
    <button type="button" class="alert-close" onclick="this.parentElement.remove()" aria-label="Đóng thông báo">
        <i class="fas fa-times" aria-hidden="true"></i>
    </button>
</div>
@endif
