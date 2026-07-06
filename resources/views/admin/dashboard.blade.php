@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">แดชบอร์ด</h4>
        <p class="text-muted small mb-0">ภาพรวมข้อมูลในระบบ SWOT</p>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-people fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">จัดการผู้ใช้งาน</div>
                            <div class="fs-3 fw-bold text-dark">{{ $userCount }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-tags fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">หมวด SWOT</div>
                            <div class="fs-3 fw-bold text-dark">{{ $categoryCount }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('admin.questions.index') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px;">
                            <i class="bi bi-question-circle fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small">ข้อคำถาม</div>
                            <div class="fs-3 fw-bold text-dark">{{ $questionCount }}</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection
