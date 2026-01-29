@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body text-center">
            <h3 class="mb-3">Xác thực email của bạn</h3>
            <p class="text-muted">
                Một email xác thực đã được gửi đến <strong>{{ auth()->user()->email ?? 'email của bạn' }}</strong>.
                Vui lòng kiểm tra hộp thư và nhấp vào liên kết xác thực.
            </p>

            @if (session('resent'))
                <div class="alert alert-success mt-3">
                    Liên kết xác thực mới đã được gửi tới email của bạn.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-primary mt-3">
                    Gửi lại email xác thực
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
