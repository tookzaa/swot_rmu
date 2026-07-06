@extends('layouts.admin')

@section('title', 'หัวข้อรอง')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.strategic-issues.index') }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left"></i> กลับไปหน้าประเด็นยุทธศาสตร์
        </a>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">หัวข้อรอง</h4>
            <p class="text-muted small mb-0">{{ $issue->name }} ({{ $subTopics->count() }} หัวข้อ)</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createSubTopicModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มหัวข้อรอง
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>รหัส</th>
                            <th>หัวข้อรอง</th>
                            <th>จำนวนตัวชี้วัด</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subTopics as $index => $subTopic)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><span class="badge text-bg-primary">{{ $subTopic->code }}</span></td>
                                <td>{{ $subTopic->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ $subTopic->indicators_count }} ตัวชี้วัด</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.strategic-sub-topics.indicators.index', $subTopic) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-list-nested"></i> ตัวชี้วัด
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSubTopicModal{{ $subTopic->id }}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteSubTopicModal{{ $subTopic->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">ยังไม่มีหัวข้อรองในประเด็นนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create sub-topic modal --}}
    <div class="modal fade" id="createSubTopicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.strategic-issues.sub-topics.store', $issue) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มหัวข้อรอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-4">
                            <label class="form-label small fw-semibold">รหัส</label>
                            <input type="text" name="code" class="form-control" maxlength="20" autocomplete="off" placeholder="เช่น 1.1" required>
                        </div>
                        <div class="col-8">
                            <label class="form-label small fw-semibold">ชื่อหัวข้อรอง</label>
                            <input type="text" name="name" class="form-control" autocomplete="off" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit / delete modals per sub-topic --}}
    @foreach ($subTopics as $subTopic)
        <div class="modal fade" id="editSubTopicModal{{ $subTopic->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.strategic-issues.sub-topics.update', [$issue, $subTopic]) }}" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขหัวข้อรอง</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-4">
                                <label class="form-label small fw-semibold">รหัส</label>
                                <input type="text" name="code" class="form-control" maxlength="20" value="{{ $subTopic->code }}" autocomplete="off" required>
                            </div>
                            <div class="col-8">
                                <label class="form-label small fw-semibold">ชื่อหัวข้อรอง</label>
                                <input type="text" name="name" class="form-control" value="{{ $subTopic->name }}" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteSubTopicModal{{ $subTopic->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.strategic-issues.sub-topics.destroy', [$issue, $subTopic]) }}" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">ยืนยันการลบหัวข้อรอง</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ต้องการลบหัวข้อรอง <strong>{{ $subTopic->code }} {{ $subTopic->name }}</strong> ใช่หรือไม่? ตัวชี้วัดทั้งหมดภายใต้หัวข้อนี้จะถูกลบด้วย การกระทำนี้ไม่สามารถย้อนกลับได้
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
        document.getElementById('createSubTopicModal')?.addEventListener('show.bs.modal', function () {
            this.querySelector('form').reset();
        });
    </script>
@endsection
