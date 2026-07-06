<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\StrategicIssue;
use Illuminate\Http\Request;

class StrategicIssueController extends Controller
{
    public function index()
    {
        $issues = StrategicIssue::withCount('subTopics')->orderBy('sort_order')->orderBy('id')->get();

        return view('admin.strategic-issues.index', [
            'issues' => $issues,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $issue = StrategicIssue::create($data);

        ActivityLogger::log('create_strategic_issue', null, 'เพิ่มประเด็นยุทธศาสตร์ #' . $issue->id);

        return redirect()->route('admin.strategic-issues.index')->with('success', 'เพิ่มประเด็นยุทธศาสตร์เรียบร้อยแล้ว');
    }

    public function update(Request $request, StrategicIssue $strategicIssue)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
        ]);

        $strategicIssue->update($data);

        ActivityLogger::log('update_strategic_issue', null, 'แก้ไขประเด็นยุทธศาสตร์ #' . $strategicIssue->id);

        return redirect()->route('admin.strategic-issues.index')->with('success', 'แก้ไขประเด็นยุทธศาสตร์เรียบร้อยแล้ว');
    }

    public function destroy(StrategicIssue $strategicIssue)
    {
        $id = $strategicIssue->id;
        $strategicIssue->delete();

        ActivityLogger::log('delete_strategic_issue', null, 'ลบประเด็นยุทธศาสตร์ #' . $id);

        return redirect()->route('admin.strategic-issues.index')->with('success', 'ลบประเด็นยุทธศาสตร์เรียบร้อยแล้ว');
    }
}
