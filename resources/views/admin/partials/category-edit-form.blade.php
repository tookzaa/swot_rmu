<div class="row g-3">
    <div class="col-4">
        <label class="form-label small fw-semibold">รหัสหมวด</label>
        <input type="text" name="code" class="form-control" maxlength="100" value="{{ $category->code }}" autocomplete="off" required>
    </div>
    <div class="col-8">
        <label class="form-label small fw-semibold">ชื่อหมวด</label>
        <input type="text" name="category_name" class="form-control" value="{{ $category->category_name }}" autocomplete="off" required>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold d-block">สถานะการโหวต</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="vote_status" id="editVoteStatusClosed{{ $category->id }}" value="{{ \App\Models\SwotCategory::VOTE_CLOSED }}" {{ $category->vote_status == \App\Models\SwotCategory::VOTE_OPEN ? '' : 'checked' }}>
            <label class="form-check-label" for="editVoteStatusClosed{{ $category->id }}">ปิดการโหวต</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="vote_status" id="editVoteStatusOpen{{ $category->id }}" value="{{ \App\Models\SwotCategory::VOTE_OPEN }}" {{ $category->vote_status == \App\Models\SwotCategory::VOTE_OPEN ? 'checked' : '' }}>
            <label class="form-check-label" for="editVoteStatusOpen{{ $category->id }}">เปิดการโหวต</label>
        </div>
    </div>
</div>
