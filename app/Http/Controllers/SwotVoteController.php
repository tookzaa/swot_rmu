<?php

namespace App\Http\Controllers;

use App\Models\QuestionSwot;
use App\Models\SwotCategory;
use App\Models\SwotVote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SwotVoteController extends Controller
{
    private const COOKIE_NAME = 'swot_voter_token';
    private const COOKIE_MINUTES = 60 * 24 * 365 * 5;

    public function index()
    {
        $categories = SwotCategory::whereIn('code', ['O', 'S', 'T', 'W'])
            ->get()
            ->keyBy('code');

        return view('vote.index', [
            'categories' => $categories,
        ]);
    }

    public function show(Request $request, SwotCategory $category)
    {
        if (! $category->isVotingOpen()) {
            return redirect()
                ->route('vote.index')
                ->with('vote_closed', 'ระบบยังไม่เปิดระบบให้โหวต กรุณารอผู้ดูแลระบบเปิดสำหรับการโหวต');
        }

        $voterToken = $request->cookie(self::COOKIE_NAME);

        $questions = $category->questions()->orderBy('id')->get();

        $myVotes = $voterToken
            ? SwotVote::whereIn('question_swot_id', $questions->pluck('id'))
                ->where('voter_token', $voterToken)
                ->pluck('score', 'question_swot_id')
            : collect();

        return view('vote.show', [
            'category' => $category,
            'questions' => $questions,
            'myVotes' => $myVotes,
        ]);
    }

    public function store(Request $request, QuestionSwot $question)
    {
        if (! $question->category->isVotingOpen()) {
            $message = 'ระบบยังไม่เปิดระบบให้โหวต กรุณารอผู้ดูแลระบบเปิดสำหรับการโหวต';

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 403);
            }

            return redirect()->route('vote.index')->with('vote_closed', $message);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        $voterToken = $request->cookie(self::COOKIE_NAME) ?: (string) Str::uuid();

        $alreadyVoted = SwotVote::where('question_swot_id', $question->id)
            ->where('voter_token', $voterToken)
            ->exists();

        if (! $alreadyVoted) {
            SwotVote::create([
                'question_swot_id' => $question->id,
                'voter_token' => $voterToken,
                'score' => $data['score'],
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'already_voted' => $alreadyVoted,
                'score' => $data['score'],
            ])->cookie(self::COOKIE_NAME, $voterToken, self::COOKIE_MINUTES);
        }

        return redirect()
            ->route('vote.show', $question->swot_category_id)
            ->with(
                $alreadyVoted ? 'error' : 'success',
                $alreadyVoted ? 'คุณได้ประเมินข้อนี้ไปแล้ว' : 'บันทึกคะแนนเรียบร้อยแล้ว'
            )
            ->cookie(self::COOKIE_NAME, $voterToken, self::COOKIE_MINUTES);
    }

    public function summary(SwotCategory $category)
    {
        return response()->json($this->computeSummary($category));
    }

    public function graph()
    {
        $categories = SwotCategory::whereIn('code', ['O', 'S', 'T', 'W'])
            ->get()
            ->keyBy('code');

        $impact = [];

        foreach (['S', 'W', 'O', 'T'] as $code) {
            $category = $categories->get($code);
            $impact[$code] = $category ? $this->computeSummary($category)['totals']['impact'] : 0;
        }

        return response()->json($impact);
    }

    private function computeSummary(SwotCategory $category): array
    {
        $questions = $category->questions()->orderBy('id')->get();

        $counts = SwotVote::whereIn('question_swot_id', $questions->pluck('id'))
            ->selectRaw('question_swot_id, score, COUNT(*) as cnt')
            ->groupBy('question_swot_id', 'score')
            ->get()
            ->groupBy('question_swot_id');

        $rows = [];
        $grandTotal = 0;

        foreach ($questions as $question) {
            $scoreCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

            foreach ($counts->get($question->id, collect()) as $row) {
                $scoreCounts[$row->score] = $row->cnt;
            }

            $total = array_sum($scoreCounts);
            $sumScore = 0;
            foreach ($scoreCounts as $score => $cnt) {
                $sumScore += $score * $cnt;
            }

            $rows[] = [
                'question_id' => $question->id,
                'total' => $total,
                'scores' => $scoreCounts,
                'average' => $total > 0 ? round($sumScore / $total, 2) : 0,
            ];

            $grandTotal += $total;
        }

        $totalScores = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $impactSum = 0;

        foreach ($rows as &$row) {
            $row['proportion'] = $grandTotal > 0 ? round($row['total'] / $grandTotal, 2) : 0;
            $row['impact'] = round($row['proportion'] * $row['average'], 2);

            foreach ($row['scores'] as $score => $cnt) {
                $totalScores[$score] += $cnt;
            }

            $impactSum += $row['impact'];
        }
        unset($row);

        return [
            'rows' => $rows,
            'totals' => [
                'total' => $grandTotal,
                'proportion' => $grandTotal > 0 ? 1 : 0,
                'scores' => $totalScores,
                'impact' => round($impactSum, 2),
            ],
        ];
    }
}
