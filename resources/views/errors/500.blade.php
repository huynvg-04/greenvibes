@extends('layouts.app')

@section('content')
@section('title', 'Không tìm thấy trang')
<div class="container px-0">
    <div class="row justify-content-center">
        <div class="col-md-8 error-container">
            <img src="{{ asset('images/undraw_page-not-found_6wni.svg') }}"
                alt="404 Illustration"
                class="img-illustration floating-anim">

            <div class="error-code text-uppercase">500</div>

            <div class="error-message">
                <h2 class="fw-bold text-dark mb-3">Ối! Lạc đường rồi...</h2>
                <p class="text-muted mb-4 fs-5">
                    Trang bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.
                    <br>Đừng lo, hãy quay về nhà nhé!
                </p>
                <div class="btn">
                    <a href="{{ url('/') }}" class="btn-home">
                        Trở về Trang chủ
                    </a>
                    <a href="javascript:history.back()" class="btn-return">
                        Quay lại
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
<style>
    body {
        font-family: var(--font-ui);
        font-size: var(--type-caption);
    }

    .error-container {
        text-align: center;
        padding: 40px;
        max-width: 600px;
    }


    .error-code {
        font-size: 6rem;
        font-weight: 900;
        line-height: 1;
        color: #e9ecef;
        position: relative;
        z-index: 1;
        animation: floating 3s ease-in-out infinite;
    }

    .error-message {
        position: relative;
        z-index: 2;
        margin-top: -50px;

    }

    .img-illustration {
        width: 100%;
        max-width: 350px;
        margin-bottom: 30px;
        drop-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .btn {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-home {
        padding: 12px 30px;
        background-color: var(--color-accent);
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid var(--color-accent);
        text-decoration: none;
    }

    .btn-home:hover {
        background-color: white;
        color: var(--color-accent);
        border: 2px solid var(--color-accent);
    }

    .btn-return {
        padding: 12px 30px;
        background-color: var(--color-accent);
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 2px solid var(--color-accent);
        text-decoration: none;
    }

    .btn-return:hover {
        background-color: white;
        color: var(--color-accent);
        border: 2px solid var(--color-accent);
    }
</style>
@endsection