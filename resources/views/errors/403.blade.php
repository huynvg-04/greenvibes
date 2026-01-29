<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Không có quyền truy cập</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        :root {
            --color-primary: #2C3E50;
            --color-accent: #7fa15a;
            --color-warning: #e67e22;
            --color-white: #FFFFFF;
            --color-muted: #7F8C8D;
            --bg-color: #fcfdfd;
            --font-ui: 'Inter', system-ui, -apple-system, sans-serif;
            --btn-padding: 12px 25px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: var(--font-ui);
            background-color: var(--bg-color);
            background-image: radial-gradient(#7fa15a 0.8px, transparent 0.8px);
            background-size: 20px 20px;
            color: var(--color-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .error-container {
            padding: 40px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        /* Animations */
        @keyframes floatImage {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .img-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .img-illustration {
            width: 100%;
            max-width: 280px;
            /* Ảnh 403 thường to hơn chút */
            height: auto;
            animation: floatImage 4s ease-in-out infinite;
        }

        .anim-item {
            opacity: 0;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
        }

        .delay-2 {
            animation-delay: 0.3s;
        }

        .delay-3 {
            animation-delay: 0.5s;
        }

        .delay-4 {
            animation-delay: 0.7s;
        }

        /* Typography */
        .error-code {
            font-size: 6rem;
            font-weight: 900;
            color: var(--color-accent);
            /* Hoặc dùng var(--color-warning) */
            line-height: 1;
            margin: 0;
            text-shadow: 3px 3px 0px rgba(127, 161, 90, 0.15);
        }

        .error-message h2 {
            font-size: 1.8rem;
            margin: 16px 0;
            color: var(--color-primary);
        }

        .error-message p {
            color: var(--color-muted);
            font-size: 1.1rem;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        /* Buttons */
        .btn-container {
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .btn {
            padding: var(--btn-padding);
            font-weight: 600;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            /* Khoảng cách giữa icon và chữ */
        }

        .btn-home {
            background-color: var(--color-accent);
            color: var(--color-white);
            border: 2px solid var(--color-accent);
            box-shadow: 0 4px 10px rgba(127, 161, 90, 0.3);
        }

        .btn-return {
            background-color: transparent;
            color: var(--color-muted);
            border: 2px solid #e0e0e0;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        .btn-home:hover {
            background: var(--color-white);
            color: var(--color-accent);
            box-shadow: 0 8px 20px rgba(127, 161, 90, 0.4);
            filter: brightness(1.05);
        }

        .btn-return:hover {
            border-color: var(--color-accent);
            color: var(--color-accent);
        }

        @media (max-width: 480px) {
            .error-code {
                font-size: 4rem;
            }

            .error-message h2 {
                font-size: 1.5rem;
            }

            .btn-container {
                flex-direction: column;
                width: 100%;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="error-container">

        <div class="img-wrapper anim-item delay-1">
            <img src="{{ asset('images/undraw_cancel_7zdh.svg') }}"
                alt="403 Access Denied"
                class="img-illustration">
        </div>

        <div class="error-code anim-item delay-2">403</div>

        <div class="error-message anim-item delay-3">
            <h2>Truy cập bị từ chối!</h2>
            <p>
                Rất tiếc, bạn không có quyền truy cập vào khu vực này.<br>
                Vui lòng liên hệ quản lý nếu bạn nghĩ đây là lỗi.
            </p>
        </div>
        <div class="btn-container anim-item delay-4">
            <a href="{{ $homeUrl ?? url('/') }}" class="btn btn-home">
                <i class='bx bxs-dashboard'></i>{{ $btnText }}
            </a>
            <a href="javascript:history.back()" class="btn btn-return">
                <i class='bx bx-arrow-back'></i>Quay lại trang trước
            </a>
        </div>
    </div>
</body>

</html>