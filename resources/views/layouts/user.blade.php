<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'หน้าแรก') | {{ config('app.name', 'SWOT') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        html, body {
            height: 100%;
        }
        body {
            background: #f4f6f9;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            background: #224abe;
            background: linear-gradient(90deg, #224abe 0%, #1b3a94 100%);
            padding: .75rem 1.5rem;
            color: #fff;
        }
        .topbar .brand {
            font-weight: 700;
            font-size: 1.1rem;
        }
        .topbar .navbar-toggler {
            border-color: rgba(255,255,255,.5);
        }
        .topbar .navbar-toggler:focus {
            box-shadow: 0 0 0 .2rem rgba(255,255,255,.25);
        }
        @media (max-width: 767.98px) {
            .topbar-menu {
                margin-top: .75rem;
                padding-top: .75rem;
                border-top: 1px solid rgba(255,255,255,.15);
                align-items: stretch !important;
            }
            .topbar-menu .btn-topbar {
                width: 100%;
            }
            .topbar-menu form {
                width: 100%;
            }
            .topbar-menu .text-end {
                text-align: left !important;
            }
        }
        .avatar-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #4e73df;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .9rem;
        }
        .content-wrapper {
            padding: 1.75rem;
            flex: 1 0 auto;
        }
        .app-footer {
            flex-shrink: 0;
            background: #1b2a4a;
            color: rgba(255,255,255,.75);
            padding: 1.5rem 1.5rem;
            margin-top: 2rem;
        }
        .app-footer .footer-title {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .app-footer .footer-meta {
            font-size: .85rem;
            line-height: 1.7;
        }
        .app-footer hr {
            border-color: rgba(255,255,255,.15);
            margin: .9rem 0;
        }
        .app-footer .footer-copyright {
            font-size: .78rem;
            color: rgba(255,255,255,.5);
        }
        .btn-topbar {
            border-radius: 999px;
            font-weight: 600;
            padding: .4rem 1rem;
            transition: transform .15s ease, box-shadow .15s ease, background-color .15s ease, color .15s ease;
        }
        .btn-topbar:hover {
            transform: translateY(-2px);
            box-shadow: 0 .35rem .75rem rgba(0,0,0,.18);
        }
        .btn-topbar.btn-outline-light:hover {
            background-color: rgba(255,255,255,.15);
            color: #fff;
        }
        .btn-topbar.btn-vote {
            background-color: #fff;
            color: #1b3a94;
            border: none;
        }
        .btn-topbar.btn-vote:hover {
            background-color: #eef1ff;
            color: #1b3a94;
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="topbar navbar navbar-expand-md navbar-dark">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap px-0">
            <div class="brand"><i class="bi bi-clipboard-data me-1"></i> ระบบประเมิน SWOT</div>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topbarMenu" aria-controls="topbarMenu" aria-expanded="false" aria-label="เปิด/ปิดเมนู">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-grow-0" id="topbarMenu">
                <div class="topbar-menu d-flex flex-column flex-md-row align-items-md-center gap-3">
                    @if (session('logged_in'))
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-semibold small">{{ session('USERFULLNAME', 'ผู้ใช้งาน') }}</div>
                            <div class="text-white-50" style="font-size: .75rem;">{{ session('role') === 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้ใช้งานทั่วไป' }}</div>
                        </div>
                        <div class="avatar-circle d-none d-md-flex">
                            {{ mb_substr(session('USERFULLNAME', 'U'), 0, 1) }}
                        </div>
                        @if (session('role') === 'admin')
                            <a href="{{ route('admin.index') }}" class="btn btn-sm btn-outline-light btn-topbar">
                                <i class="bi bi-speedometer2 me-1"></i> แดชบอร์ด
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-light btn-topbar">
                                <i class="bi bi-box-arrow-right me-1"></i> ออกจากระบบ
                            </button>
                        </form>
                    @else
                        <a href="{{ route('home.index') }}" class="btn btn-sm btn-outline-light btn-topbar">
                            <i class="bi bi-house-door me-1"></i> หน้าแรก
                        </a>
                        <a href="{{ route('vote.index') }}" class="btn btn-sm btn-topbar btn-vote">
                            <i class="bi bi-check2-circle me-1"></i> โหวต SWOT
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light btn-topbar">
                            <i class="bi bi-box-arrow-in-right me-1"></i> เข้าสู่ระบบ
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="app-footer">
        <div class="container-fluid text-center">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="footer-title justify-content-center">
                        <i class="bi bi-clipboard-data"></i> ระบบประเมิน SWOT
                    </div>
                    <div class="footer-meta mt-2">
                        ออกแบบและพัฒนาโดย นายเจษฎา กลิ่นกล้า<br>
                        กองนโยบายและแผน สังกัดสำนักงานอธิการบดี มหาวิทยาลัยราชภัฏมหาสารคาม
                    </div>
                </div>
            </div>
            <hr>
            <div class="footer-copyright">
                &copy; {{ date('Y') }} ระบบประเมิน SWOT สงวนลิขสิทธิ์
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
