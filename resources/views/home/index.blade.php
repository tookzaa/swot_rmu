@extends('layouts.user')

@section('title', 'หน้าแรก')

@push('styles')
    <style>
        .page-hero {
            background: linear-gradient(120deg, #224abe 0%, #1b3a94 60%, #16a085 140%);
            border-radius: 1rem;
            padding: 2rem 2rem;
            color: #fff;
            margin-bottom: 2rem;
            box-shadow: 0 .75rem 2rem rgba(27,58,148,.25);
            position: relative;
            overflow: hidden;
        }
        .page-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 85% 20%, rgba(255,255,255,.15), transparent 55%);
        }
        .page-hero h2 {
            font-weight: 800;
            margin-bottom: .35rem;
        }
        .page-hero p {
            opacity: .9;
            margin-bottom: 0;
        }
        .section-heading {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-bottom: 1.1rem;
        }
        .section-heading .icon-badge {
            width: 40px;
            height: 40px;
            border-radius: .65rem;
            background: #eef1ff;
            color: #1b3a94;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }
        .section-heading h4 {
            margin: 0;
            font-weight: 700;
        }
        .section-heading p {
            margin: 0;
            font-size: .85rem;
        }

        .swot-category-card {
            position: relative;
            aspect-ratio: 2 / 1;
            border-radius: 1rem;
            box-shadow: 0 .35rem 1rem rgba(0,0,0,.14);
            padding: 1rem;
            overflow: hidden;
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .swot-category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(160deg, rgba(255,255,255,.22), transparent 55%);
        }
        .swot-category-card::after {
            content: '';
            position: absolute;
            right: -18px;
            bottom: -18px;
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
        }
        .swot-category-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 .75rem 1.75rem rgba(0,0,0,.22);
        }
        .swot-category-icon {
            position: relative;
            z-index: 1;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.85);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #fff;
            margin-bottom: .5rem;
        }
        .swot-category-name {
            position: relative;
            z-index: 1;
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
        }

        .issue-card {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 .35rem 1rem rgba(0,0,0,.08);
            transition: box-shadow .2s ease, transform .2s ease;
        }
        .issue-card:hover {
            box-shadow: 0 .6rem 1.5rem rgba(0,0,0,.12);
            transform: translateY(-3px);
        }
        .issue-card .card-header {
            background: linear-gradient(90deg, #16a085 0%, #12886f 100%);
            color: #fff;
            font-weight: 700;
            border: none;
            padding: .9rem 1.1rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .issue-card .list-group-item {
            border-color: rgba(0,0,0,.06);
            padding: .75rem 1.1rem;
            transition: background-color .15s ease;
        }
        .issue-card .list-group-item:hover {
            background-color: #f4f8ff;
        }
        .issue-card .badge {
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
    <div class="page-hero">
        <h2><i class="bi bi-clipboard-data me-2"></i>ระบบประเมิน SWOT</h2>
        <p>เลือกหมวด SWOT หรือประเด็นยุทธศาสตร์ด้านล่างเพื่อเริ่มทำแบบประเมิน</p>
    </div>

    <div class="section-heading">
        <div class="icon-badge"><i class="bi bi-grid-1x2"></i></div>
        <div>
            <h4>หมวด SWOT</h4>
            <p class="text-muted">ข้อคำถามแยกตามหมวด SWOT ทั้งหมด</p>
        </div>
    </div>

    @php
        $swotPalette = [
            'S' => ['color' => '#16a085', 'icon' => 'bi-journal-bookmark'],
            'W' => ['color' => '#1b3a94', 'icon' => 'bi-layers'],
            'O' => ['color' => '#c0392b', 'icon' => 'bi-list-ul'],
            'T' => ['color' => '#f39c12', 'icon' => 'bi-exclamation-triangle'],
        ];
        $fallbackPalette = [
            ['color' => '#16a085', 'icon' => 'bi-journal-bookmark'],
            ['color' => '#1b3a94', 'icon' => 'bi-layers'],
            ['color' => '#c0392b', 'icon' => 'bi-list-ul'],
            ['color' => '#f39c12', 'icon' => 'bi-exclamation-triangle'],
            ['color' => '#8e44ad', 'icon' => 'bi-diagram-3'],
            ['color' => '#2c3e50', 'icon' => 'bi-flag'],
        ];
    @endphp

    <div class="row g-3 mb-5">
        @forelse ($categories as $index => $category)
            @php
                $codeKey = strtoupper(trim($category->code));
                $style = $swotPalette[$codeKey] ?? $fallbackPalette[$index % count($fallbackPalette)];
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <a
                    href="{{ route('swot.answer.show', $category) }}"
                    class="text-decoration-none swot-category-card d-flex flex-column align-items-center justify-content-center text-center"
                    style="background-color: {{ $style['color'] }};"
                >
                    <div class="swot-category-icon">
                        <i class="bi {{ $style['icon'] }}"></i>
                    </div>
                    <div class="swot-category-name">{{ $category->category_name }}</div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">ยังไม่มีหมวด SWOT ในระบบ</p>
            </div>
        @endforelse
    </div>

    <div class="section-heading">
        <div class="icon-badge"><i class="bi bi-signpost-split"></i></div>
        <div>
            <h4>ประเด็นยุทธศาสตร์</h4>
            <p class="text-muted">ตัวชี้วัดแยกตามประเด็นยุทธศาสตร์ทั้งหมด</p>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($issues as $issue)
            <div class="col-md-6">
                <div class="card issue-card h-100">
                    <div class="card-header">
                        <i class="bi bi-flag-fill"></i>
                        {{ $issue->name }}
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse ($issue->subTopics as $subTopic)
                            <a href="{{ route('strategic.answer.show', $subTopic) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <span>
                                    <span class="badge text-bg-secondary me-1">{{ $subTopic->code }}</span>
                                    {{ $subTopic->name }}
                                </span>
                                <i class="bi bi-chevron-right text-muted"></i>
                            </a>
                        @empty
                            <p class="text-muted small mb-0 p-3">ยังไม่มีหัวข้อรองในประเด็นนี้</p>
                        @endforelse
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">ยังไม่มีประเด็นยุทธศาสตร์ในระบบ</p>
            </div>
        @endforelse
    </div>
@endsection
