<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'แดชบอร์ด') | {{ config('app.name', 'SWOT') }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background: #f4f6f9;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: #224abe;
            background: linear-gradient(180deg, #224abe 0%, #1b3a94 100%);
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.85);
            padding: .75rem 1.25rem;
            font-size: .92rem;
            border-left: 3px solid transparent;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
            border-left-color: #fff;
            font-weight: 600;
        }
        .sidebar .nav-link i {
            width: 1.25rem;
            display: inline-block;
        }
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            box-shadow: 0 .1rem .3rem rgba(0,0,0,.08);
            padding: .75rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .content-wrapper {
            padding: 1.75rem;
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
        @media (max-width: 767.98px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-clipboard-data me-1"></i> ระบบ SWOT
        </div>
        <div class="nav flex-column py-2">
            <a href="{{ route('admin.index') }}" class="nav-link {{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> แดชบอร์ด
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> จัดการผู้ใช้งาน
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> จัดการหมวด SWOT
            </a>
            <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <i class="bi bi-question-circle"></i> จัดการข้อคำถาม
            </a>
            <a href="{{ route('admin.strategic-issues.index') }}" class="nav-link {{ request()->routeIs('admin.strategic-issues.*') || request()->routeIs('admin.strategic-sub-topics.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> จัดการประเด็นยุทธศาสตร์
            </a>
            <a href="{{ route('admin.answer-report.index') }}" class="nav-link {{ request()->routeIs('admin.answer-report.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> รายงานคำตอบ SWOT
            </a>
        </div>
    </nav>

    <div class="main-content">
        <div class="topbar">
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div></div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-end d-none d-sm-block">
                    <div class="fw-semibold small">{{ session('USERFULLNAME', 'ผู้ใช้งาน') }}</div>
                    <div class="text-muted" style="font-size: .75rem;">{{ session('role') === 'admin' ? 'ผู้ดูแลระบบ' : 'ผู้ใช้งานทั่วไป' }}</div>
                </div>
                <div class="avatar-circle">
                    {{ mb_substr(session('USERFULLNAME', 'U'), 0, 1) }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-box-arrow-right"></i> ออกจากระบบ
                    </button>
                </form>
            </div>
        </div>

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
    </div>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
