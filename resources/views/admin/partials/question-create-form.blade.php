<div class="row g-3">
    <div class="col-12">
        <label class="form-label small fw-semibold">หมวด SWOT</label>
        <select name="swot_category_id" class="form-select" required>
            <option value="">-- เลือกหมวด --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->code }} - {{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label small fw-semibold">ข้อคำถาม</label>
        <textarea name="question_name" class="form-control" rows="3" autocomplete="off" required></textarea>
    </div>
</div>
