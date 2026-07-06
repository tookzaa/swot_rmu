@extends('layouts.user')

@section('title', 'โหวต SWOT - ' . $category->category_name)

@push('styles')
    <style>
        .vote-table {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        .vote-table th,
        .vote-table td {
            border: 1px solid #000;
            padding: .6rem .75rem;
            vertical-align: middle;
        }
        .vote-table thead th {
            background-color: #d9d3ae;
            text-align: center;
            font-weight: 700;
        }
        .vote-table tbody td.vote-item-cell {
            background-color: #f5d9f0;
        }
        .vote-table tbody td.vote-index-cell {
            background-color: #f5d9f0;
            text-align: center;
            width: 2.5rem;
        }
        .vote-score-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            background-color: #1b6a8c;
            color: #fff;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 .2rem;
        }
        .vote-score-btn:hover:not(:disabled) {
            background-color: #144f68;
        }
        .vote-score-btn.selected {
            background-color: #dc3545;
        }
        .vote-score-btn:disabled {
            opacity: .55;
        }
        .vote-score-cell {
            text-align: center;
            white-space: nowrap;
        }
        .summary-table {
            border: 1px solid #000;
            border-collapse: collapse;
            width: 100%;
        }
        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: .5rem .6rem;
            vertical-align: middle;
            text-align: center;
        }
        .summary-table thead th {
            background-color: #d9d3ae;
            font-weight: 700;
        }
        .summary-table tbody td.summary-item-cell {
            background-color: #f5d9f0;
            text-align: left;
        }
        .summary-table tbody td.summary-index-cell {
            background-color: #f5d9f0;
        }
        .summary-table tbody td.summary-score-cell {
            background-color: #fdf6d8;
        }
        .summary-table tfoot td {
            background-color: #eee;
            font-weight: 700;
        }
    </style>
@endpush

@php
    $categoryLabels = ['S' => 'จุดแข็ง', 'W' => 'จุดอ่อน', 'O' => 'โอกาส', 'T' => 'อุปสรรค'];
    $categoryLabel = $categoryLabels[strtoupper(trim($category->code))] ?? $category->category_name;
@endphp

@section('content')
    <div class="mb-3">
        <a href="{{ route('vote.index') }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left"></i> กลับไปหน้าโหวต
        </a>
    </div>

    <div class="mb-4">
        <h4 class="fw-bold mb-1">ส่วนการโหวต - {{ $categoryLabel }}</h4>
        <p class="text-muted small mb-0">กรุณาให้คะแนนแต่ละข้อ 1 (น้อยที่สุด) ถึง 5 (มากที่สุด) ท่านสามารถโหวตแต่ละข้อได้เพียงครั้งเดียว</p>
    </div>

    <div id="voteAlert" class="alert d-none" role="alert"></div>

    @if ($questions->isEmpty())
        <p class="text-muted">ยังไม่มีข้อคำถามในหมวดนี้</p>
    @else
        <div class="table-responsive">
            <table class="vote-table">
                <thead>
                    <tr>
                        <th colspan="2">{{ $categoryLabel }}</th>
                        <th>คะแนน</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($questions as $index => $question)
                        @php $myScore = $myVotes->get($question->id); @endphp
                        <tr data-vote-row data-question-id="{{ $question->id }}">
                            <td class="vote-index-cell">{{ $index + 1 }}</td>
                            <td class="vote-item-cell">{{ $question->question_name }}</td>
                            <td class="vote-score-cell">
                                @for ($score = 1; $score <= 5; $score++)
                                    <button
                                        type="button"
                                        class="vote-score-btn {{ $myScore == $score ? 'selected' : '' }}"
                                        data-score-btn
                                        data-score="{{ $score }}"
                                        {{ $myScore ? 'disabled' : '' }}
                                    >{{ $score }}</button>
                                @endfor
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">สรุปผลการโหวต</h5>
                <span class="badge text-bg-success" id="summaryLiveIndicator">
                    <i class="bi bi-broadcast me-1"></i> กำลังอัปเดต...
                </span>
            </div>

            <div class="table-responsive">
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th colspan="2">{{ $categoryLabel }}</th>
                            <th>จำนวน</th>
                            <th>สัดส่วน</th>
                            <th>คะแนน 1</th>
                            <th>คะแนน 2</th>
                            <th>คะแนน 3</th>
                            <th>คะแนน 4</th>
                            <th>คะแนน 5</th>
                            <th>รวม</th>
                            <th>เฉลี่ย</th>
                            <th>ผลกระทบ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $index => $question)
                            <tr data-summary-row data-question-id="{{ $question->id }}">
                                <td class="summary-index-cell">{{ $index + 1 }}</td>
                                <td class="summary-item-cell">{{ $question->question_name }}</td>
                                <td data-field="total">0</td>
                                <td data-field="proportion">0.00</td>
                                <td class="summary-score-cell" data-field="score_1">0</td>
                                <td class="summary-score-cell" data-field="score_2">0</td>
                                <td class="summary-score-cell" data-field="score_3">0</td>
                                <td class="summary-score-cell" data-field="score_4">0</td>
                                <td class="summary-score-cell" data-field="score_5">0</td>
                                <td data-field="total_dup">0</td>
                                <td data-field="average">0.00</td>
                                <td data-field="impact">0.00</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2">รวม</td>
                            <td data-total-field="total">0</td>
                            <td data-total-field="proportion">0.00</td>
                            <td data-total-field="score_1">0</td>
                            <td data-total-field="score_2">0</td>
                            <td data-total-field="score_3">0</td>
                            <td data-total-field="score_4">0</td>
                            <td data-total-field="score_5">0</td>
                            <td data-total-field="total_dup">0</td>
                            <td></td>
                            <td data-total-field="impact">0.00</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            const voteStoreUrlTemplate = @json(route('vote.store', ['question' => '__QUESTION_ID__']));

            function showVoteAlert(message, isError) {
                const alertBox = document.getElementById('voteAlert');
                alertBox.textContent = message;
                alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
                alertBox.classList.add(isError ? 'alert-danger' : 'alert-success');
                clearTimeout(window.__voteAlertTimeout);
                window.__voteAlertTimeout = setTimeout(() => alertBox.classList.add('d-none'), 2500);
            }

            document.querySelectorAll('[data-vote-row]').forEach((row) => {
                const questionId = row.dataset.questionId;
                const url = voteStoreUrlTemplate.replace('__QUESTION_ID__', questionId);

                row.querySelectorAll('[data-score-btn]').forEach((btn) => {
                    btn.addEventListener('click', function () {
                        const formData = new FormData();
                        formData.append('score', btn.dataset.score);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: formData,
                        })
                            .then((response) => response.json())
                            .then((data) => {
                                row.querySelectorAll('[data-score-btn]').forEach((otherBtn) => {
                                    otherBtn.classList.remove('selected');
                                    otherBtn.disabled = true;
                                    if (Number(otherBtn.dataset.score) === Number(data.score)) {
                                        otherBtn.classList.add('selected');
                                    }
                                });

                                showVoteAlert(
                                    data.already_voted ? 'คุณได้ประเมินข้อนี้ไปแล้ว' : 'บันทึกคะแนนเรียบร้อยแล้ว',
                                    data.already_voted
                                );

                                refreshSummary();
                            })
                            .catch(() => {
                                showVoteAlert('เกิดข้อผิดพลาด ไม่สามารถบันทึกคะแนนได้', true);
                            });
                    });
                });
            });

            const summaryUrl = @json(route('vote.summary', $category));

            function refreshSummary() {
                fetch(summaryUrl, { headers: { 'Accept': 'application/json' } })
                    .then((response) => response.json())
                    .then((data) => {
                        data.rows.forEach((row) => {
                            const rowEl = document.querySelector('[data-summary-row][data-question-id="' + row.question_id + '"]');
                            if (!rowEl) {
                                return;
                            }

                            rowEl.querySelector('[data-field="total"]').textContent = row.total;
                            rowEl.querySelector('[data-field="proportion"]').textContent = row.proportion.toFixed(2);
                            for (let s = 1; s <= 5; s++) {
                                rowEl.querySelector('[data-field="score_' + s + '"]').textContent = row.scores[s] ?? 0;
                            }
                            rowEl.querySelector('[data-field="total_dup"]').textContent = row.total;
                            rowEl.querySelector('[data-field="average"]').textContent = row.average.toFixed(2);
                            rowEl.querySelector('[data-field="impact"]').textContent = row.impact.toFixed(2);
                        });

                        const totals = data.totals;
                        document.querySelector('[data-total-field="total"]').textContent = totals.total;
                        document.querySelector('[data-total-field="proportion"]').textContent = totals.proportion.toFixed(2);
                        for (let s = 1; s <= 5; s++) {
                            document.querySelector('[data-total-field="score_' + s + '"]').textContent = totals.scores[s] ?? 0;
                        }
                        document.querySelector('[data-total-field="total_dup"]').textContent = totals.total;
                        document.querySelector('[data-total-field="impact"]').textContent = totals.impact.toFixed(2);

                        const indicator = document.getElementById('summaryLiveIndicator');
                        indicator.classList.remove('text-bg-danger');
                        indicator.classList.add('text-bg-success');
                    })
                    .catch(() => {
                        const indicator = document.getElementById('summaryLiveIndicator');
                        indicator.classList.remove('text-bg-success');
                        indicator.classList.add('text-bg-danger');
                    });
            }

            refreshSummary();
            setInterval(refreshSummary, 4000);
        </script>
    @endpush
@endsection
