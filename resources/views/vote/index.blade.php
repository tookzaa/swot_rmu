@extends('layouts.user')

@section('title', 'หน้าการโหวต SWOT')

@push('styles')
    <style>
        .vote-category-card {
            aspect-ratio: 2 / 1;
            border-radius: .75rem;
            box-shadow: 0 .25rem .75rem rgba(0,0,0,.12);
            padding: 1rem;
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .vote-category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 .5rem 1.25rem rgba(0,0,0,.18);
        }
        .vote-category-card.is-closed {
            cursor: not-allowed;
            opacity: .65;
        }
        .vote-category-card.is-closed:hover {
            transform: none;
            box-shadow: 0 .25rem .75rem rgba(0,0,0,.12);
        }
        .vote-category-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,.85);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
            margin-bottom: .75rem;
        }
        .vote-category-name {
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .swot-graph-card {
            background: #fff;
            border-radius: .75rem;
            box-shadow: 0 .25rem .75rem rgba(0,0,0,.08);
            padding: 1.5rem;
        }
        .swot-graph-wrap {
            max-width: 480px;
            margin: 0 auto;
            position: relative;
            padding: 2.25rem 6.5rem;
        }
        .swot-graph-quadrant-label {
            font-size: .7rem;
            font-weight: 700;
            fill: #6c757d;
        }
        .swot-graph-value {
            font-size: .75rem;
            font-weight: 700;
            fill: #dc3545;
        }
        .swot-axis-label {
            position: absolute;
            font-weight: 700;
            font-size: .85rem;
            white-space: nowrap;
        }
        .swot-axis-label.top {
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            color: #b6412c;
        }
        .swot-axis-label.bottom {
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            color: #e8a33d;
        }
        .swot-axis-label.right {
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            color: #3f9e79;
        }
        .swot-axis-label.left {
            top: 50%;
            left: 0;
            transform: translateY(-50%);
            color: #1f3a93;
        }
    </style>
@endpush

@section('content')
    <div class="mb-4">
        <h4 class="fw-bold mb-1">หน้าการโหวต SWOT</h4>
    </div>

    @php
        $order = [
            'O' => ['label' => 'Opportunity', 'color' => '#b6412c', 'icon' => 'bi-list-ul'],
            'S' => ['label' => 'Strength', 'color' => '#3f9e79', 'icon' => 'bi-journal-bookmark'],
            'T' => ['label' => 'Threat', 'color' => '#e8a33d', 'icon' => 'bi-exclamation-triangle'],
            'W' => ['label' => 'Weakness', 'color' => '#1f3a93', 'icon' => 'bi-layers'],
        ];
    @endphp


    <div class="row g-3">
        @foreach ($order as $code => $style)
            @php
                $category = $categories->get($code);
                $isVotingOpen = $category && $category->vote_status == \App\Models\SwotCategory::VOTE_OPEN;
            @endphp
            <div class="col-6 col-md-3">
                @if ($category && $isVotingOpen)
                    <a
                        href="{{ route('vote.show', $category) }}"
                        class="text-decoration-none vote-category-card d-flex flex-column align-items-center justify-content-center text-center"
                        style="background-color: {{ $style['color'] }};"
                    >
                        <div class="vote-category-icon">
                            <i class="bi {{ $style['icon'] }}"></i>
                        </div>
                        <div class="vote-category-name">{{ $category->category_name }}</div>
                    </a>
                @elseif ($category)
                    <div
                        class="vote-category-card is-closed d-flex flex-column align-items-center justify-content-center text-center"
                        style="background-color: {{ $style['color'] }};"
                        data-vote-closed
                        role="button"
                    >
                        <div class="vote-category-icon">
                            <i class="bi {{ $style['icon'] }}"></i>
                        </div>
                        <div class="vote-category-name">{{ $category->category_name }}</div>
                    </div>
                @else
                    <div
                        class="vote-category-card d-flex flex-column align-items-center justify-content-center text-center opacity-50"
                        style="background-color: {{ $style['color'] }};"
                    >
                        <div class="vote-category-icon">
                            <i class="bi {{ $style['icon'] }}"></i>
                        </div>
                        <div class="vote-category-name">{{ $style['label'] }}</div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="swot-graph-card">
                <h5 class="fw-bold mb-3 text-center">แผนภาพ SWOT (ตามแนวคิด ผู้ช่วยศาสตราจารย์ ดร.เฉลิมเกียรติ ดุลสัมพันธ์)</h5>
                <div class="swot-graph-wrap">
                    <div class="swot-axis-label top">โอกาส (O)</div>
                    <div class="swot-axis-label bottom">อุปสรรค (T)</div>
                    <div class="swot-axis-label right">จุดแข็ง (S)</div>
                    <div class="swot-axis-label left">จุดอ่อน (W)</div>
                    <svg id="swotGraph" viewBox="-105 -105 210 210" width="100%" style="display: block;"></svg>
                </div>
                <p class="text-muted small text-center mt-3 mb-0">
                    ค่าที่แสดงคือ &quot;ผลกระทบ&quot; (impact) เฉลี่ยถ่วงน้ำหนักจากผลโหวตของแต่ละหมวด (0-5)
                    โดยพื้นที่ของรูปสี่เหลี่ยมในจตุภาคใดมากที่สุด บ่งชี้ทิศทางกลยุทธ์หลักขององค์กรในจตุภาคนั้น
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-vote-closed]').forEach((card) => {
                card.addEventListener('click', function () {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ยังไม่เปิดให้โหวต',
                        text: 'ระบบยังไม่เปิดระบบให้โหวต กรุณารอผู้ดูแลระบบเปิดสำหรับการโหวต',
                        confirmButtonText: 'รับทราบ',
                    });
                });
            });

            @if (session('vote_closed'))
                Swal.fire({
                    icon: 'warning',
                    title: 'ยังไม่เปิดให้โหวต',
                    text: @json(session('vote_closed')),
                    confirmButtonText: 'รับทราบ',
                });
            @endif
        </script>
        <script>
            (function () {
                const svgNS = 'http://www.w3.org/2000/svg';
                const svg = document.getElementById('swotGraph');
                const scale = 18; // px per 1.0 impact point (max impact = 5 -> 90px)
                const maxValue = 5;

                function el(tag, attrs, text) {
                    const node = document.createElementNS(svgNS, tag);
                    Object.entries(attrs || {}).forEach(([key, value]) => node.setAttribute(key, value));
                    if (text !== undefined) {
                        node.textContent = text;
                    }
                    return node;
                }

                function render(data) {
                    const s = data.S || 0;
                    const w = data.W || 0;
                    const o = data.O || 0;
                    const t = data.T || 0;

                    svg.innerHTML = '';

                    // quadrant backgrounds
                    const quadrants = [
                        { x: 0, y: -100, w: 100, h: 100, color: '#eaf5ef', label: 'SO เชิงรุก', lx: 50, ly: -55 },
                        { x: -100, y: -100, w: 100, h: 100, color: '#eef1fb', label: 'WO เชิงแก้ไข', lx: -50, ly: -55 },
                        { x: 0, y: 0, w: 100, h: 100, color: '#fdf3e3', label: 'ST เชิงป้องกัน', lx: 50, ly: 60 },
                        { x: -100, y: 0, w: 100, h: 100, color: '#fbeaea', label: 'WT เชิงรับ', lx: -50, ly: 60 },
                    ];
                    quadrants.forEach((q) => {
                        svg.appendChild(el('rect', { x: q.x, y: q.y, width: q.w, height: q.h, fill: q.color }));
                        svg.appendChild(el('text', { x: q.lx, y: q.ly, 'text-anchor': 'middle', class: 'swot-graph-quadrant-label' }, q.label));
                    });

                    // reference grid circles (1..5)
                    for (let i = 1; i <= maxValue; i++) {
                        svg.appendChild(el('circle', {
                            cx: 0, cy: 0, r: i * scale, fill: 'none', stroke: '#dee2e6', 'stroke-width': 0.5,
                        }));
                    }

                    // axes
                    svg.appendChild(el('line', { x1: -100, y1: 0, x2: 100, y2: 0, stroke: '#adb5bd', 'stroke-width': 1 }));
                    svg.appendChild(el('line', { x1: 0, y1: -100, x2: 0, y2: 100, stroke: '#adb5bd', 'stroke-width': 1 }));

                    // data kite polygon: top=O, right=S, bottom=T, left=W
                    const points = [
                        [0, -o * scale],
                        [s * scale, 0],
                        [0, t * scale],
                        [-w * scale, 0],
                    ].map((p) => p.join(',')).join(' ');

                    svg.appendChild(el('polygon', {
                        points,
                        fill: 'rgba(220,53,69,.25)',
                        stroke: '#dc3545',
                        'stroke-width': 2,
                    }));

                    // value labels at each vertex, offset inward (toward center) so they never
                    // approach the viewBox edge, regardless of how large the value is
                    svg.appendChild(el('text', { x: 0, y: -o * scale + 18, 'text-anchor': 'middle', class: 'swot-graph-value' }, o.toFixed(2)));
                    svg.appendChild(el('text', { x: s * scale - 10, y: -6, 'text-anchor': 'end', class: 'swot-graph-value' }, s.toFixed(2)));
                    svg.appendChild(el('text', { x: 0, y: t * scale - 10, 'text-anchor': 'middle', class: 'swot-graph-value' }, t.toFixed(2)));
                    svg.appendChild(el('text', { x: -w * scale + 10, y: -6, 'text-anchor': 'start', class: 'swot-graph-value' }, w.toFixed(2)));
                }

                fetch(@json(route('vote.graph')), { headers: { 'Accept': 'application/json' } })
                    .then((response) => response.json())
                    .then(render)
                    .catch(() => render({ S: 0, W: 0, O: 0, T: 0 }));
            })();
        </script>
    @endpush
@endsection
