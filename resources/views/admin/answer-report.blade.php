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
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <p class="fw-semibold mb-0">{{ $index + 1 }}. <span class="question-text">{{ $question->question_name }}</span></p>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger flex-shrink-0"
                                data-bs-toggle="modal"
                                data-bs-target="#editQuestionModal{{ $question->id }}"
                            >
                                <i class="bi bi-pencil-square me-1"></i> แก้ไข
                            </button>
                        </div>
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

    {{-- Edit question modals (dedicated to this page, saves via AJAX without leaving the report) --}}
    @foreach ($categories as $category)
        @foreach ($category->questions as $question)
            <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form
                        method="POST"
                        action="{{ route('admin.questions.update', $question) }}"
                        class="modal-content"
                        data-report-edit-form
                        data-question-id="{{ $question->id }}"
                    >
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="swot_category_id" value="{{ $question->swot_category_id }}">
                        <div class="modal-header">
                            <h5 class="modal-title">แก้ไขข้อคำถาม</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label small fw-semibold">ข้อคำถาม</label>
                            <textarea name="question_name" class="form-control" rows="3" autocomplete="off" required>{{ $question->question_name }}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                            <button type="submit" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm d-none" data-submit-spinner></span>
                                บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
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

        document.querySelectorAll('[data-report-edit-form]').forEach((form) => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const spinner = form.querySelector('[data-submit-spinner]');
                submitBtn.disabled = true;
                spinner.classList.remove('d-none');

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: new FormData(form),
                })
                    .then((response) => response.json().then((data) => ({ ok: response.ok, data })))
                    .then(({ ok, data }) => {
                        if (!ok) {
                            const message = data.errors
                                ? Object.values(data.errors).flat().join('\n')
                                : (data.message || 'เกิดข้อผิดพลาด ไม่สามารถบันทึกได้');
                            throw new Error(message);
                        }

                        const questionId = form.dataset.questionId;
                        const item = document.querySelector('[data-question-id="' + questionId + '"]');
                        item.querySelector('.question-text').textContent = data.question.question_name;

                        const modalEl = form.closest('.modal');
                        bootstrap.Modal.getInstance(modalEl)?.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'บันทึกการแก้ไขเรียบร้อยแล้ว',
                            timer: 1800,
                            showConfirmButton: false,
                        });
                    })
                    .catch((error) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message || 'ไม่สามารถบันทึกข้อคำถามได้',
                        });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        spinner.classList.add('d-none');
                    });
            });
        });
    </script>
@endsection