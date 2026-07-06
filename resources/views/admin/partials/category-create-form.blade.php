<div class="row g-3">
    <div class="col-4">
        <label class="form-label small fw-semibold">รหัสหมวด</label>
        <input type="text" name="code" class="form-control" maxlength="100" value="" autocomplete="off" required>
    </div>
    <div class="col-8">
        <label class="form-label small fw-semibold">ชื่อหมวด</label>
        <input type="text" name="category_name" class="form-control" value="" autocomplete="off" required>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold d-block">สถานะการโหวต</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="vote_status" id="createVoteStatusClosed" value="{{ \App\Models\SwotCategory::VOTE_CLOSED }}" checked>
            <label class="form-check-label" for="createVoteStatusClosed">ปิดการโหวต</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="vote_status" id="createVoteStatusOpen" value="{{ \App\Models\SwotCategory::VOTE_OPEN }}">
            <label class="form-check-label" for="createVoteStatusOpen">เปิดการโหวต</label>
        </div>
    </div>
</div>
