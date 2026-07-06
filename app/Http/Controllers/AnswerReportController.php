<?php

namespace App\Http\Controllers;

use App\Models\AnswerSwot;
use App\Models\SwotCategory;

class AnswerReportController extends Controller
{
    public function index()
    {
        $categories = SwotCategory::with('questions')->orderBy('code')->get();

        return view('admin.answer-report', [
            'categories' => $categories,
        ]);
    }

    public function data()
    {
        $keepCounts = AnswerSwot::where('answer_type', AnswerSwot::TYPE_KEEP)
            ->selectRaw('question_swot_id, COUNT(*) as total')
            ->groupBy('question_swot_id')
            ->pluck('total', 'question_swot_id');

        $edited = AnswerSwot::with('user')
            ->where('answer_type', AnswerSwot::TYPE_EDITED)
            ->latest('id')
            ->get()
            ->groupBy('question_swot_id')
            ->map(function ($answers) {
                return $answers->map(fn ($answer) => [
                    'detail' => $answer->answer_detail,
                    'user' => $answer->user->fullname ?? 'ไม่ระบุตัวตน',
                    'created_at' => optional($answer->created_at)->format('d/m/Y H:i'),
                ]);
            });

        return response()->json([
            'keep_counts' => $keepCounts,
            'edited' => $edited,
        ]);
    }
}
