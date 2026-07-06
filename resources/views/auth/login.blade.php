<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>เข้าสู่ระบบ | {{ config('app.name', 'SWOT') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
            overflow: hidden;
        }
        .login-card .card-header {
            background: #fff;
            border-bottom: none;
            text-align: center;
            padding: 2.5rem 2rem 1rem;
        }
        .login-card .card-header .logo-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #4e73df;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: #fff;
            font-size: 1.75rem;
            font-weight: 700;
        }
        .btn-login {
            background: #4e73df;
            border: none;
            padding: .65rem 1rem;
            font-weight: 600;
        }
        .btn-login:hover {
            background: #375ac4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-11 col-sm-8 col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-header">
                        <div class="logo-circle">S</div>
                        <h4 class="fw-bold mb-1">ระบบ SWOT</h4>
                        <p class="text-muted small mb-0">เข้าสู่ระบบด้วยบัญชีผู้ใช้งาน RMU</p>
                    </div>
                    <div class="card-body px-4 pb-4">
                        @if (session('error'))
                            <div class="alert alert-danger py-2 small mb-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.authenticate') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="username" class="form-label small fw-semibold">ชื่อผู้ใช้งาน</label>
                                <input
                                    type="text"
                                    class="form-control @error('username') is-invalid @enderror"
                                    id="username"
                                    name="username"
                                    value="{{ old('username') }}"
                                    autofocus
                                    autocomplete="username"
                                >
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label small fw-semibold">รหัสผ่าน</label>
                                <input
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    autocomplete="current-password"
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-login btn-primary w-100 text-white">
                                เข้าสู่ระบบ
                            </button>
                            <a href="{{ route('home.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="bi bi-house-door me-1"></i> กลับหน้าแรก
                            </a>
                        </form>
                    </div>
                </div>
                <p class="text-center text-white-50 small mt-3 mb-0">&copy; {{ date('Y') }} Rajabhat Mahasarakham University</p>
            </div>
        </div>
    </div>
</body>
</html>
