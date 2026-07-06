@extends('layouts.admin')

@section('title', 'จัดการประเด็นยุทธศาสตร์')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">จัดการประเด็นยุทธศาสตร์</h4>
            <p class="text-muted small mb-0">ประเด็นยุทธศาสตร์ทั้งหมด ({{ $issues->count() }} ประเด็น)</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIssueModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มประเด็นยุทธศาสตร์
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>ประเด็นยุทธศาสตร์</th>
                            <th>จำนวนหัวข้อรอง</th>
                            <th class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($issues as $index => $issue)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $issue->name }}</td>
                                <td><span class="badge text-bg-secondary">{{ $issue->sub_topics_count }} หัวข้อ</span></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('admin.strategic-issues.sub-topics.index', $issue) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-list-nested"></i> หัวข้อรอง
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editIssueModal{{ $issue->id }}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteIssueModal{{ $issue->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">ยังไม่มีประเด็นยุทธศาสตร์ในระบบ</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create issue modal --}}
    <div class="modal fade" id="createIssueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.strategic-issues.store') }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มประเด็นยุทธศาสตร์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">ชื่อประเด็นยุทธศาสตร์</label>
                        <textarea name="name" class="form-control" rows="2" autocomplete="off" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit / delete modals per issue --}}
    @foreach ($issues as $issue)
        <div class="modal fade" id="editIssueModal{{ $issue->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.strategic-issues.update', $issue) }}" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขประเด็นยุทธศาสตร์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">ชื่อประเด็นยุทธศาสตร์</label>
                            <textarea name="name" class="form-control" rows="2" autocomplete="off" required>{{ $issue->name }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteIssueModal{{ $issue->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.strategic-issues.destroy', $issue) }}" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">ยืนยันการลบประเด็นยุทธศาสตร์</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ต้องการลบประเด็นยุทธศาสตร์นี้ใช่หรือไม่? หัวข้อรองและตัวชี้วัดทั้งหมดภายใต้ประเด็นนี้จะถูกลบด้วย การกระทำนี้ไม่สามารถย้อนกลับได้
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
        document.getElementById('createIssueModal')?.addEventListener('show.bs.modal', function () {
            this.querySelector('form').reset();
        });
    </script>
@endsection
