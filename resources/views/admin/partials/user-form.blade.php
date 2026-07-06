@php
    $user = $user ?? null;
@endphp

<div class="row g-3">
    <div class="col-12">
        <label class="form-label small fw-semibold">รหัสผู้ใช้ (MISID)</label>
        <input type="text" name="misid" class="form-control" value="{{ old('misid', $user->misid ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold">ชื่อผู้ใช้งาน (Username)</label>
        <input type="text" name="username" class="form-control" value="{{ old('username', $user->username ?? '') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold">ชื่อ-สกุล</label>
        <input type="text" name="fullname" class="form-control" value="{{ old('fullname', $user->fullname ?? '') }}" required>
    </div>
    <div class="col-6">
        <label class="form-label small fw-semibold">รหัสหน่วยงาน</label>
        <input type="text" name="facultyid" class="form-control" value="{{ old('facultyid', $user->facultyid ?? '') }}">
    </div>
    <div class="col-6">
        <label class="form-label small fw-semibold">ชื่อหน่วยงาน</label>
        <input type="text" name="faculty_name" class="form-control" value="{{ old('faculty_name', $user->faculty_name ?? '') }}">
    </div>
    <div class="col-6">
        <label class="form-label small fw-semibold">สิทธิ์การใช้งาน</label>
        <select name="role" class="form-select" required>
            <option value="user" @selected(old('role', $user->role ?? 'user') === 'user')>ผู้ใช้งานทั่วไป</option>
            <option value="admin" @selected(old('role', $user->role ?? 'user') === 'admin')>ผู้ดูแลระบบ</option>
        </select>
    </div>
    <div class="col-6 d-flex align-items-end">
        <div class="form-check form-switch">
            <input
                type="checkbox"
                class="form-check-input"
                name="is_active"
                value="1"
                id="isActive{{ $user->id ?? 'new' }}"
                @checked(old('is_active', $user->is_active ?? true))
            >
            <label class="form-check-label small" for="isActive{{ $user->id ?? 'new' }}">เปิดใช้งานบัญชี</label>
        </div>
    </div>
</div>
