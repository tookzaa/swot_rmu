<div class="row g-3">
    <div class="col-4">
        <label class="form-label small fw-semibold">รหัสหมวด</label>
        <input type="text" name="code" class="form-control" maxlength="100" value="{{ $category->code }}" autocomplete="off" required>
    </div>
    <div class="col-8">
        <label class="form-label small fw-semibold">ชื่อหมวด</label>
        <input type="text" name="category_name" class="form-control" value="{{ $category->category_name }}" autocomplete="off" required>
    </div>
</div>
