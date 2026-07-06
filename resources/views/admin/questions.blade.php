@extends('layouts.admin')

@section('title', 'จัดการข้อคำถาม')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">จัดการข้อคำถาม</h4>
            <p class="text-muted small mb-0">ข้อคำถาม SWOT ทั้งหมด ({{ $questions->count() }} ข้อ)</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มข้อคำถาม
        </button>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.questions.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-semibold mb-1">กรองตามหมวด SWOT</label>
                    <select name="swot_category_id" class="form-select" onchange="this.form.submit()">
                        <option value="">-- ทั้งหมด --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId == $category->id)>
                                {{ $category->code }} - {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($selectedCategoryId)
                    <div class="col-auto">
                        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">ล้างตัวกรอง</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>หมวด</th>
                            <th>ข้อคำถาม</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge text-bg-primary">{{ $question->category->code ?? '-' }}</span></td>
                                <td>{{ $question->question_name }}</td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editQuestionModal{{ $question->id }}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteQuestionModal{{ $question->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">ยังไม่มีข้อคำถามในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create question modal --}}
    <div class="modal fade" id="createQuestionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.questions.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มข้อคำถาม</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.partials.question-create-form')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit / delete modals per question --}}
    @foreach ($questions as $question)
        <div class="modal fade" id="editQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.questions.update', $question) }}" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขข้อคำถาม</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        @include('admin.partials.question-edit-form', ['question' => $question])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteQuestionModal{{ $question->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">ยืนยันการลบข้อคำถาม</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ต้องการลบข้อคำถามนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-danger">ลบ</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach

    <script>
        document.getElementById('createQuestionModal')?.addEventListener('show.bs.modal', function () {
            this.querySelector('form').reset();
        });
    </script>
@endsection
