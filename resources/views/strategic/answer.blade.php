@extends('layouts.user')

@section('title', 'ประเด็นยุทธศาสตร์')

@section('content')
    <div class="mb-3">
        <a href="{{ route('home.index') }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left"></i> กลับไปหน้าแรก
        </a>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <span class="badge text-bg-secondary me-1">{{ $subTopic->code }}</span>
            {{ $subTopic->name }}
        </h4>
        <p class="text-muted small mb-0">ตัวชี้วัดในหัวข้อรองนี้ ({{ $indicators->count() }} ข้อ)</p>
    </div>

    <div id="answerAlert" class="alert alert-success d-none" role="alert">บันทึกคำตอบเรียบร้อยแล้ว</div>

    @forelse ($indicators as $index => $indicator)
        @php
            $keepCount = $indicator->answers->where('answer_type', \App\Models\StrategicIndicatorAnswer::TYPE_KEEP)->count();
            $editedAnswers = $indicator->answers->where('answer_type', \App\Models\StrategicIndicatorAnswer::TYPE_EDITED)->values();
            $myAnswer = $myAnswers->get($indicator->id);
        @endphp
        <div class="card border-0 shadow-sm mb-3" data-answer-item data-item-id="{{ $indicator->id }}">
            <div class="card-body">
                <p class="mb-3">{{ $index + 1 }}. {{ $indicator->name }}</p>

                <p class="fw-semibold small mb-2">ผล/ค่าเป้าหมาย</p>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered text-center align-middle mb-0" style="max-width: 500px;">
                        <thead class="table-light">
                            <tr>
                                @foreach ($years as $year)
                                    <th class="{{ $loop->last ? 'text-danger' : '' }}">{{ $year }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($years as $year)
                                    <td class="{{ $loop->last ? 'text-danger fw-bold' : '' }}">
                                        {{ $indicator->targets->firstWhere('year', $year)->target_value ?? '-' }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>

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
                        <form method="POST" action="{{ route('strategic.answer.store', $indicator) }}" data-answer-form>
                            @csrf
                            <input type="hidden" name="answer_type" value="{{ \App\Models\StrategicIndicatorAnswer::TYPE_KEEP }}">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-check-circle me-1"></i> คงเดิม
                            </button>
                        </form>
                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            data-bs-toggle="collapse"
                            data-bs-target="#editBox{{ $indicator->id }}"
                        >
                            <i class="bi bi-pencil-square me-1"></i> แก้ไข
                        </button>
                    </div>

                    <div class="collapse mt-3" id="editBox{{ $indicator->id }}">
                        <form method="POST" action="{{ route('strategic.answer.store', $indicator) }}" data-answer-form>
                            @csrf
                            <input type="hidden" name="answer_type" value="{{ \App\Models\StrategicIndicatorAnswer::TYPE_EDITED }}">
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
                        @if ($myAnswer?->answer_type == \App\Models\StrategicIndicatorAnswer::TYPE_EDITED)
                            แก้ไข ({{ $myAnswer->answer_detail }})
                        @elseif ($myAnswer)
                            คงเดิม
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @empty
        <p class="text-muted">ยังไม่มีตัวชี้วัดในหัวข้อรองนี้</p>
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
