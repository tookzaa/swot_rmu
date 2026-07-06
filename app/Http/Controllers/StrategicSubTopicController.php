<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\StrategicIssue;
use App\Models\StrategicSubTopic;
use Illuminate\Http\Request;

class StrategicSubTopicController extends Controller
{
    public function index(StrategicIssue $strategicIssue)
    {
        $subTopics = $strategicIssue->subTopics()->withCount('indicators')->get();

        return view('admin.strategic-issues.sub-topics', [
            'issue' => $strategicIssue,
            'subTopics' => $subTopics,
        ]);
    }

    public function store(Request $request, StrategicIssue $strategicIssue)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $data['strategic_issue_id'] = $strategicIssue->id;

        StrategicSubTopic::create($data);

        ActivityLogger::log('create_strategic_sub_topic', null, 'เพิ่มหัวข้อรองในประเด็นยุทธศาสตร์ #' . $strategicIssue->id);

        return redirect()->route('admin.strategic-issues.sub-topics.index', $strategicIssue)->with('success', 'เพิ่มหัวข้อรองเรียบร้อยแล้ว');
    }

    public function update(Request $request, StrategicIssue $strategicIssue, StrategicSubTopic $subTopic)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $subTopic->update($data);

        ActivityLogger::log('update_strategic_sub_topic', null, 'แก้ไขหัวข้อรอง #' . $subTopic->id);

        return redirect()->route('admin.strategic-issues.sub-topics.index', $strategicIssue)->with('success', 'แก้ไขหัวข้อรองเรียบร้อยแล้ว');
    }

    public function destroy(StrategicIssue $strategicIssue, StrategicSubTopic $subTopic)
    {
        $id = $subTopic->id;
        $subTopic->delete();

        ActivityLogger::log('delete_strategic_sub_topic', null, 'ลบหัวข้อรอง #' . $id);

        return redirect()->route('admin.strategic-issues.sub-topics.index', $strategicIssue)->with('success', 'ลบหัวข้อรองเรียบร้อยแล้ว');
    }
}
