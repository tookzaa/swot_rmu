<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\QuestionSwot;
use App\Models\SwotCategory;
use Illuminate\Http\Request;

class QuestionSwotController extends Controller
{
    public function index(Request $request)
    {
        $selectedCategoryId = $request->query('swot_category_id');

        $questions = QuestionSwot::with('category')
            ->when($selectedCategoryId, fn ($query) => $query->where('swot_category_id', $selectedCategoryId))
            ->orderByDesc('id')
            ->get();
        $categories = SwotCategory::orderBy('code')->get();

        return view('admin.questions', [
            'questions' => $questions,
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question_name' => 'required|string',
            'swot_category_id' => 'required|exists:swot_categories,id',
        ]);

        QuestionSwot::create($data);

        ActivityLogger::log('create_question_swot', null, 'เพิ่มข้อคำถาม SWOT');

        return redirect()->route('admin.questions.index')->with('success', 'เพิ่มข้อคำถามเรียบร้อยแล้ว');
    }

    public function update(Request $request, QuestionSwot $question)
    {
        $data = $request->validate([
            'question_name' => 'required|string',
            'swot_category_id' => 'required|exists:swot_categories,id',
        ]);

        $question->update($data);

        ActivityLogger::log('update_question_swot', null, 'แก้ไขข้อคำถาม SWOT #' . $question->id);

        return redirect()->route('admin.questions.index')->with('success', 'แก้ไขข้อคำถามเรียบร้อยแล้ว');
    }

    public function destroy(QuestionSwot $question)
    {
        $id = $question->id;
        $question->delete();

        ActivityLogger::log('delete_question_swot', null, 'ลบข้อคำถาม SWOT #' . $id);

        return redirect()->route('admin.questions.index')->with('success', 'ลบข้อคำถามเรียบร้อยแล้ว');
    }
}
