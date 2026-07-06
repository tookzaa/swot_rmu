<?php

namespace App\Http\Controllers;

use App\Models\StrategicIssue;
use App\Models\SwotCategory;

class HomeController extends Controller
{
    public function index()
    {
        $categories = SwotCategory::orderBy('code')->get();

        $issues = StrategicIssue::with('subTopics')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('home.index', [
            'categories' => $categories,
            'issues' => $issues,
        ]);
    }
}
