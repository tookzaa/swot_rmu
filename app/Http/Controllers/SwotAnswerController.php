<?php

namespace App\Http\Controllers;

use App\Models\AnswerSwot;
use App\Models\QuestionSwot;
use App\Models\SwotCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SwotAnswerController extends Controller
{
    private const COOKIE_NAME = 'swot_answer_token';
    private const COOKIE_MINUTES = 60 * 24 * 365 * 5;

    public function show(Request $request, SwotCategory $category)
    {
        $userId = Session::get('user_id');
        $respondentToken = $request->cookie(self::COOKIE_NAME);

        $questions = $category->questions()->with('answers')->get();

        $myAnswers = $respondentToken
            ? AnswerSwot::whereIn('question_swot_id', $questions->pluck('id'))
                ->where('respondent_token', $respondentToken)
                ->get()
                ->keyBy('question_swot_id')
            : collect();

        return view('swot.answer', [
            'category' => $category,
            'questions' => $questions,
            'userId' => $userId,
            'myAnswers' => $myAnswers,
        ]);
    }

    public function store(Request $request, QuestionSwot $question)
    {
        $data = $request->validate([
            'answer_type' => 'required|in:' . AnswerSwot::TYPE_KEEP . ',' . AnswerSwot::TYPE_EDITED,
            'answer_detail' => 'nullable|string|max:2000|required_if:answer_type,' . AnswerSwot::TYPE_EDITED,
        ]);

        if (isset($data['answer_detail'])) {
            $data['answer_detail'] = strip_tags(trim($data['answer_detail']));
        }

        $respondentToken = $request->cookie(self::COOKIE_NAME) ?: (string) Str::uuid();

        $alreadyAnswered = AnswerSwot::where('question_swot_id', $question->id)
            ->where('respondent_token', $respondentToken)
            ->exists();

        if (! $alreadyAnswered) {
            AnswerSwot::create([
                'question_swot_id' => $question->id,
                'user_id' => Session::get('user_id'),
                'respondent_token' => $respondentToken,
                'answer_type' => $data['answer_type'],
                'answer_detail' => $data['answer_type'] == AnswerSwot::TYPE_EDITED ? $data['answer_detail'] : null,
            ]);
        }

        $answers = $question->answers()->get();
        $myAnswer = $answers->firstWhere('respondent_token', $respondentToken);

        if ($request->wantsJson()) {
            return response()->json([
                'already_answered' => $alreadyAnswered,
                'keep_count' => $answers->where('answer_type', AnswerSwot::TYPE_KEEP)->count(),
                'edited' => $answers->where('answer_type', AnswerSwot::TYPE_EDITED)->values()->pluck('answer_detail'),
                'my_answer_type' => $myAnswer?->answer_type,
                'my_answer_detail' => $myAnswer?->answer_type == AnswerSwot::TYPE_EDITED ? $myAnswer->answer_detail : '',
            ])->cookie(self::COOKIE_NAME, $respondentToken, self::COOKIE_MINUTES);
        }

        return redirect()
            ->route('swot.answer.show', $question->swot_category_id)
            ->with(
                $alreadyAnswered ? 'error' : 'success',
                $alreadyAnswered ? 'คุณตอบข้อนี้ไปแล้ว' : 'บันทึกคำตอบเรียบร้อยแล้ว'
            )
            ->cookie(self::COOKIE_NAME, $respondentToken, self::COOKIE_MINUTES);
    }
}
