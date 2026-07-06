@extends('layouts.admin')

@section('title', 'ตัวชี้วัด')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.strategic-issues.sub-topics.index', $subTopic->strategic_issue_id) }}" class="text-decoration-none small">
            <i class="bi bi-arrow-left"></i> กลับไปหน้าหัวข้อรอง
        </a>
    </div>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">ตัวชี้วัด</h4>
            <p class="text-muted small mb-0">{{ $subTopic->code }} {{ $subTopic->name }} ({{ $indicators->count() }} ตัวชี้วัด)</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createIndicatorModal">
            <i class="bi bi-plus-lg me-1"></i> เพิ่มตัวชี้วัด
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle text-start">ตัวชี้วัด</th>
                            <th colspan="{{ count($years) }}">ผล/ค่าเป้าหมาย</th>
                            <th rowspan="2" class="align-middle">จัดการ</th>
                        </tr>
                        <tr>
                            @foreach ($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($indicators as $index => $indicator)
                            <tr>
                                <td class="text-start">{{ $index + 1 }}. {{ $indicator->name }}</td>
                                @foreach ($years as $year)
                                    <td>{{ $indicator->targets->firstWhere('year', $year)->target_value ?? '-' }}</td>
                                @endforeach
                                <td>
                                    <div class="d-flex justify-content-center gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editIndicatorModal{{ $indicator->id }}"
                                        >
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteIndicatorModal{{ $indicator->id }}"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($years) + 2 }}" class="text-center text-muted py-4">ยังไม่มีตัวชี้วัดในหัวข้อนี้</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create indicator modal --}}
    <div class="modal fade" id="createIndicatorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('admin.strategic-sub-topics.indicators.store', $subTopic) }}" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">เพิ่มตัวชี้วัด</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">ตัวชี้วัด</label>
                        <textarea name="name" class="form-control" rows="2" autocomplete="off" required></textarea>
                    </div>
                    <label class="form-label small fw-semibold">ผล/ค่าเป้าหมาย</label>
                    <div class="row g-2">
                        @foreach ($years as $year)
                            <div class="col">
                                <label class="form-label small text-muted mb-1">{{ $year }}</label>
                                <input type="text" name="targets[{{ $year }}]" class="form-control" autocomplete="off">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit / delete modals per indicator --}}
    @foreach ($indicators as $indicator)
        <div class="modal fade" id="editIndicatorModal{{ $indicator->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ route('admin.strategic-sub-topics.indicators.update', [$subTopic, $indicator]) }}" class="modal-content">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">แก้ไขตัวชี้วัด</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">ตัวชี้วัด</label>
                            <textarea name="name" class="form-control" rows="2" autocomplete="off" required>{{ $indicator->name }}</textarea>
                        </div>
                        <label class="form-label small fw-semibold">ผล/ค่าเป้าหมาย</label>
                        <div class="row g-2">
                            @foreach ($years as $year)
                                <div class="col">
                                    <label class="form-label small text-muted mb-1">{{ $year }}</label>
                                    <input type="text" name="targets[{{ $year }}]" class="form-control" value="{{ $indicator->targets->firstWhere('year', $year)->target_value ?? '' }}" autocomplete="off">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="deleteIndicatorModal{{ $indicator->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('admin.strategic-sub-topics.indicators.destroy', [$subTopic, $indicator]) }}" class="modal-content">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">ยืนยันการลบตัวชี้วัด</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        ต้องการลบตัวชี้วัดนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้
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
        document.getElementById('createIndicatorModal')?.addEventListener('show.bs.modal', function () {
            this.querySelector('form').reset();
        });
    </script>
@endsection
