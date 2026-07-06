<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\SwotCategory;
use Illuminate\Http\Request;

class SwotCategoryController extends Controller
{
    public function index()
    {
        $categories = SwotCategory::orderBy('code')->get();

        return view('admin.categories', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:100|unique:swot_categories,code',
            'category_name' => 'required|string|max:10000',
            'vote_status' => 'nullable|integer|in:' . SwotCategory::VOTE_CLOSED . ',' . SwotCategory::VOTE_OPEN,
        ]);

        $data['vote_status'] = $data['vote_status'] ?? SwotCategory::VOTE_CLOSED;

        $category = SwotCategory::create($data);

        ActivityLogger::log('create_swot_category', null, 'เพิ่มหมวด SWOT ' . $category->category_name);

        return redirect()->route('admin.categories.index')->with('success', 'เพิ่มหมวด SWOT เรียบร้อยแล้ว');
    }


    public function update(Request $request, SwotCategory $category)
    {
        $data = $request->validate([
            'code' => 'required|string|max:100|unique:swot_categories,code,' . $category->id,
            'category_name' => 'required|string|max:100',
            'vote_status' => 'nullable|integer|in:' . SwotCategory::VOTE_CLOSED . ',' . SwotCategory::VOTE_OPEN,
        ]);

        $data['vote_status'] = $data['vote_status'] ?? SwotCategory::VOTE_CLOSED;

        $category->update($data);

        ActivityLogger::log('update_swot_category', null, 'แก้ไขหมวด SWOT ' . $category->category_name);

        return redirect()->route('admin.categories.index')->with('success', 'แก้ไขหมวด SWOT เรียบร้อยแล้ว');
    }

    public function destroy(SwotCategory $category)
    {
        $name = $category->category_name;
        $category->delete();

        ActivityLogger::log('delete_swot_category', null, 'ลบหมวด SWOT ' . $name);

        return redirect()->route('admin.categories.index')->with('success', 'ลบหมวด SWOT เรียบร้อยแล้ว');
    }
}
