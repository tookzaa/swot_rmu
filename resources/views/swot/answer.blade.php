@extends('layouts.user')

@section('title', 'ทำแบบสำรวจ SWOT')

@section('content')
    <div class="mb-3">
        <a href="{{ route('home.index') }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left"></i> กลับไปหน้าแรก
        </a>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <span class="badge text-bg-primary me-1">{{ $category->code }}</span>
            {{ $category->category_name }}
        </h4>
        <p class="text-muted small mb-0">กรุณาตอบข้อคำถามแต่ละข้อด้านล่าง ({{ $questions->count() }} ข้อ)</p>
    </div>

    <div id="answerAlert" class="alert alert-success d-none" role="alert">บันทึกคำตอบเรียบร้อยแล้ว</div>

    @forelse ($questions as $index => $question)
        @php
            $keepCount = $question->answers->where('answer_type', \App\Models\AnswerSwot::TYPE_KEEP)->count();
            $editedAnswers = $question->answers->where('answer_type', \App\Models\AnswerSwot::TYPE_EDITED)->values();
            $myAnswer = $myAnswers->get($question->id);
        @endphp
        <div class="card border-0 shadow-sm mb-3" data-answer-item data-item-id="{{ $question->id }}">
            <div class="card-body">
                <p class="mb-3">{{ $index + 1 }}. {{ $question->question_name }}</p>

                <div class="d-flex flex-wrap gap-5 mb-3">
                    <div class="small">
                        จำนวน คงเดิม <strong data-keep-count>{{ $keepCount }}</strong> ครั้ง
                    </div>
                    <div class="small">
                        รายการแก้ไข
                        <div data-edited-list>
                            @if ($editedAnswers->isEmpty())
                                <span class="text-muted">ยังไม่มีรายการแก้ไข</span>
                            @else
                                <ol class="mb-0 ps-3">
                                    @foreach ($editedAnswers as $edited)
                                        <li>{{ $edited->answer_detail }}</li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    </div>
                </div>

                <div data-answer-actions class="{{ $myAnswer ? 'd-none' : '' }}">
                    <div class="d-flex gap-2">
                        <form method="POST" action="{{ route('swot.answer.store', $question) }}" data-answer-form>
                            @csrf
                            <input type="hidden" name="answer_type" value="{{ \App\Models\AnswerSwot::TYPE_KEEP }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-check-circle me-1"></i> คงเดิม
                            </button>
                        </form>
                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="collapse"
                            data-bs-target="#editBox{{ $question->id }}"
                        >
                            <i class="bi bi-pencil-square me-1"></i> แก้ไข
                        </button>
                    </div>

                    <div class="collapse mt-3" id="editBox{{ $question->id }}">
                        <form method="POST" action="{{ route('swot.answer.store', $question) }}" data-answer-form>
                            @csrf
                            <input type="hidden" name="answer_type" value="{{ \App\Models\AnswerSwot::TYPE_EDITED }}">
                            <textarea name="answer_detail" class="form-control mb-2" rows="3" autocomplete="off" placeholder="กรอกคำตอบที่แก้ไข" data-answer-textarea></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i> บันทึกการแก้ไข
                            </button>
                        </form>
                    </div>
                </div>

                <div class="small text-success {{ $myAnswer ? '' : 'd-none' }}" data-answer-done>
                    <i class="bi bi-check-circle-fill me-1"></i>
                    คุณได้ตอบข้อนี้แล้ว:
                    <span data-answer-summary>
                        @if ($myAnswer?->answer_type == \App\Models\AnswerSwot::TYPE_EDITED)
                            แก้ไข ({{ $myAnswer->answer_detail }})
                        @elseif ($myAnswer)
                            คงเดิม
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">ยังไม่มีข้อคำถามในหมวดนี้</p>
    @endforelse

    @push('scripts')
        <script>
            document.querySelectorAll('[data-answer-form]').forEach((form) => {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const item = form.closest('[data-answer-item]');
                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: formData,
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            item.querySelector('[data-keep-count]').textContent = data.keep_count;

                            const listEl = item.querySelector('[data-edited-list]');
                            if (data.edited.length === 0) {
                                listEl.innerHTML = '<span class="text-muted">ยังไม่มีรายการแก้ไข</span>';
                            } else {
                                listEl.innerHTML = '<ol class="mb-0 ps-3">' + data.edited.map((text) => {
                                    const li = document.createElement('li');
                                    li.textContent = text;
                                    return li.outerHTML;
                                }).join('') + '</ol>';
                            }

                            item.querySelector('[data-answer-actions]').classList.add('d-none');

                            const doneEl = item.querySelector('[data-answer-done]');
                            doneEl.classList.remove('d-none');
                            doneEl.querySelector('[data-answer-summary]').textContent =
                                data.my_answer_type == 2 ? `แก้ไข (${data.my_answer_detail})` : 'คงเดิม';

                            const alertBox = document.getElementById('answerAlert');
                            if (data.already_answered) {
                                alertBox.textContent = 'คุณตอบข้อนี้ไปแล้ว';
                            } else {
                                alertBox.textContent = 'บันทึกคำตอบเรียบร้อยแล้ว';
                            }
                            alertBox.classList.remove('d-none');
                            clearTimeout(window.__answerAlertTimeout);
                            window.__answerAlertTimeout = setTimeout(() => alertBox.classList.add('d-none'), 2500);
                        })
                        .catch(() => {
                            alert('เกิดข้อผิดพลาด ไม่สามารถบันทึกคำตอบได้');
                        });
                });
            });
        </script>
    @endpush
@endsection
