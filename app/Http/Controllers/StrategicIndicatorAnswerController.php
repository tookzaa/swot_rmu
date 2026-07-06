<?php

namespace App\Http\Controllers;

use App\Models\StrategicIndicator;
use App\Models\StrategicIndicatorAnswer;
use App\Models\StrategicSubTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class StrategicIndicatorAnswerController extends Controller
{
    private const COOKIE_NAME = 'strategic_answer_token';
    private const COOKIE_MINUTES = 60 * 24 * 365 * 5;

    public function show(Request $request, StrategicSubTopic $subTopic)
    {
        $userId = Session::get('user_id');
        $respondentToken = $request->cookie(self::COOKIE_NAME);

        $indicators = $subTopic->indicators()->with('answers', 'targets')->get();

        $myAnswers = $respondentToken
            ? StrategicIndicatorAnswer::whereIn('strategic_indicator_id', $indicators->pluck('id'))
                ->where('respondent_token', $respondentToken)
                ->get()
                ->keyBy('strategic_indicator_id')
            : collect();

        return view('strategic.answer', [
            'subTopic' => $subTopic,
            'indicators' => $indicators,
            'userId' => $userId,
            'myAnswers' => $myAnswers,
            'years' => StrategicIndicatorController::YEARS,
        ]);
    }

    public function store(Request $request, StrategicIndicator $indicator)
    {
        $data = $request->validate([
            'answer_type' => 'required|in:' . StrategicIndicatorAnswer::TYPE_KEEP . ',' . StrategicIndicatorAnswer::TYPE_EDITED,
            'answer_detail' => 'nullable|string|max:2000|required_if:answer_type,' . StrategicIndicatorAnswer::TYPE_EDITED,
        ]);

        if (isset($data['answer_detail'])) {
            $data['answer_detail'] = strip_tags(trim($data['answer_detail']));
        }

        $respondentToken = $request->cookie(self::COOKIE_NAME) ?: (string) Str::uuid();

        $alreadyAnswered = StrategicIndicatorAnswer::where('strategic_indicator_id', $indicator->id)
            ->where('respondent_token', $respondentToken)
            ->exists();

        if (! $alreadyAnswered) {
            StrategicIndicatorAnswer::create([
                'strategic_indicator_id' => $indicator->id,
                'user_id' => Session::get('user_id'),
                'respondent_token' => $respondentToken,
                'answer_type' => $data['answer_type'],
                'answer_detail' => $data['answer_type'] == StrategicIndicatorAnswer::TYPE_EDITED ? $data['answer_detail'] : null,
            ]);
        }

        $answers = $indicator->answers()->get();
        $myAnswer = $answers->firstWhere('respondent_token', $respondentToken);

        if ($request->wantsJson()) {
            return response()->json([
                'already_answered' => $alreadyAnswered,
                'keep_count' => $answers->where('answer_type', StrategicIndicatorAnswer::TYPE_KEEP)->count(),
                'edited' => $answers->where('answer_type', StrategicIndicatorAnswer::TYPE_EDITED)->values()->pluck('answer_detail'),
                'my_answer_type' => $myAnswer?->answer_type,
                'my_answer_detail' => $myAnswer?->answer_type == StrategicIndicatorAnswer::TYPE_EDITED ? $myAnswer->answer_detail : '',
            ])->cookie(self::COOKIE_NAME, $respondentToken, self::COOKIE_MINUTES);
        }

        return redirect()
            ->route('strategic.answer.show', $indicator->strategic_sub_topic_id)
            ->with(
                $alreadyAnswered ? 'error' : 'success',
                $alreadyAnswered ? 'คุณตอบข้อนี้ไปแล้ว' : 'บันทึกคำตอบเรียบร้อยแล้ว'
            )
            ->cookie(self::COOKIE_NAME, $respondentToken, self::COOKIE_MINUTES);
    }
}
