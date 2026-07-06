<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\StrategicIndicator;
use App\Models\StrategicSubTopic;
use Illuminate\Http\Request;

class StrategicIndicatorController extends Controller
{
    public const YEARS = [2566, 2567, 2568, 2569, 2570];

    public function index(StrategicSubTopic $subTopic)
    {
        $indicators = $subTopic->indicators()->with('targets')->get();

        return view('admin.strategic-issues.indicators', [
            'subTopic' => $subTopic,
            'indicators' => $indicators,
            'years' => self::YEARS,
        ]);
    }

    public function store(Request $request, StrategicSubTopic $subTopic)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
            'targets' => 'nullable|array',
            'targets.*' => 'nullable|string|max:50',
        ]);

        $indicator = $subTopic->indicators()->create([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->syncTargets($indicator, $data['targets'] ?? []);

        ActivityLogger::log('create_strategic_indicator', null, 'เพิ่มตัวชี้วัดในหัวข้อรอง #' . $subTopic->id);

        return redirect()->route('admin.strategic-sub-topics.indicators.index', $subTopic)->with('success', 'เพิ่มตัวชี้วัดเรียบร้อยแล้ว');
    }

    public function update(Request $request, StrategicSubTopic $subTopic, StrategicIndicator $indicator)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sort_order' => 'nullable|integer',
            'targets' => 'nullable|array',
            'targets.*' => 'nullable|string|max:50',
        ]);

        $indicator->update([
            'name' => $data['name'],
            'sort_order' => $data['sort_order'] ?? 0,
        ]);

        $this->syncTargets($indicator, $data['targets'] ?? []);

        ActivityLogger::log('update_strategic_indicator', null, 'แก้ไขตัวชี้วัด #' . $indicator->id);

        return redirect()->route('admin.strategic-sub-topics.indicators.index', $subTopic)->with('success', 'แก้ไขตัวชี้วัดเรียบร้อยแล้ว');
    }

    public function destroy(StrategicSubTopic $subTopic, StrategicIndicator $indicator)
    {
        $id = $indicator->id;
        $indicator->delete();

        ActivityLogger::log('delete_strategic_indicator', null, 'ลบตัวชี้วัด #' . $id);

        return redirect()->route('admin.strategic-sub-topics.indicators.index', $subTopic)->with('success', 'ลบตัวชี้วัดเรียบร้อยแล้ว');
    }

    private function syncTargets(StrategicIndicator $indicator, array $targets): void
    {
        foreach (self::YEARS as $year) {
            $value = $targets[$year] ?? null;

            $indicator->targets()->updateOrCreate(
                ['year' => $year],
                ['target_value' => $value]
            );
        }
    }
}
