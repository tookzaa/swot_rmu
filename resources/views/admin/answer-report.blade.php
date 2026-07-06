@extends('layouts.admin')

@section('title', 'รายงานคำตอบ SWOT')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">รายงานคำตอบ SWOT</h4>
            <p class="text-muted small mb-0">อัปเดตอัตโนมัติแบบเรียลไทม์เมื่อมีการตอบใหม่</p>
        </div>
        <span class="badge text-bg-success" id="liveIndicator">
            <i class="bi bi-broadcast me-1"></i> กำลังอัปเดต...
        </span>
    </div>

    @foreach ($categories as $category)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <span class="badge text-bg-light text-primary me-1">{{ $category->code }}</span>
                {{ $category->category_name }}
            </div>
            <div class="card-body">
                @forelse ($category->questions as $index => $question)
                    <div class="mb-4 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}" data-question-id="{{ $question->id }}">
                        <p class="fw-semibold mb-2">{{ $index + 1 }}. {{ $question->question_name }}</p>
                        <div class="mb-2">
                            <span class="badge text-bg-secondary">
                                คงเดิม: <span class="keep-count">0</span> ครั้ง
                            </span>
                        </div>
                        <div class="edited-list small text-muted">ยังไม่มีรายการแก้ไข</div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">ยังไม่มีข้อคำถามในหมวดนี้</p>
                @endforelse
            </div>
        </div>
    @endforeach

    <script>
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function refreshAnswerReport() {
            fetch('{{ route('admin.answer-report.data') }}')
                .then((response) => response.json())
                .then((data) => {
                    document.querySelectorAll('[data-question-id]').forEach((el) => {
                        const questionId = el.dataset.questionId;

                        const keepCount = data.keep_counts[questionId] ?? 0;
                        el.querySelector('.keep-count').textContent = keepCount;

                        const editedItems = data.edited[questionId] ?? [];
                        const listEl = el.querySelector('.edited-list');

                        if (editedItems.length === 0) {
                            listEl.innerHTML = 'ยังไม่มีรายการแก้ไข';
                        } else {
                            listEl.innerHTML = '<ul class="mb-0 ps-3">' + editedItems.map((item) =>
                                `<li>${escapeHtml(item.detail)}</li>`
                            ).join('') + '</ul>';
                        }
                    });
                })
                .catch(() => {
                    document.getElementById('liveIndicator').classList.replace('text-bg-success', 'text-bg-danger');
                });
        }

        refreshAnswerReport();
        setInterval(refreshAnswerReport, 4000);
    </script>
@endsection