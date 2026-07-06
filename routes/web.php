<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SwotCategoryController;
use App\Http\Controllers\QuestionSwotController;
use App\Http\Controllers\StrategicIssueController;
use App\Http\Controllers\StrategicSubTopicController;
use App\Http\Controllers\StrategicIndicatorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SwotAnswerController;
use App\Http\Controllers\SwotVoteController;
use App\Http\Controllers\AnswerReportController;
use App\Http\Controllers\StrategicIndicatorAnswerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home.index');
});

Route::get('/home', [HomeController::class, 'index'])->name('home.index');

Route::get('/swot/categories/{category}', [SwotAnswerController::class, 'show'])->name('swot.answer.show');
Route::post('/swot/questions/{question}/answer', [SwotAnswerController::class, 'store'])->name('swot.answer.store');

Route::get('/vote', [SwotVoteController::class, 'index'])->name('vote.index');
Route::get('/vote/graph', [SwotVoteController::class, 'graph'])->name('vote.graph');
Route::get('/vote/categories/{category}', [SwotVoteController::class, 'show'])->name('vote.show');
Route::get('/vote/categories/{category}/summary', [SwotVoteController::class, 'summary'])->name('vote.summary');
Route::post('/vote/questions/{question}/answer', [SwotVoteController::class, 'store'])->name('vote.store');

Route::get('/strategic/sub-topics/{subTopic}', [StrategicIndicatorAnswerController::class, 'show'])->name('strategic.answer.show');
Route::post('/strategic/indicators/{indicator}/answer', [StrategicIndicatorAnswerController::class, 'store'])->name('strategic.answer.store');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.session')->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');

    Route::get('/admin/categories', [SwotCategoryController::class, 'index'])->name('admin.categories.index');
    Route::post('/admin/categories', [SwotCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [SwotCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [SwotCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::get('/admin/answer-report', [AnswerReportController::class, 'index'])->name('admin.answer-report.index');
    Route::get('/admin/answer-report/data', [AnswerReportController::class, 'data'])->name('admin.answer-report.data');

    Route::get('/admin/questions', [QuestionSwotController::class, 'index'])->name('admin.questions.index');
    Route::post('/admin/questions', [QuestionSwotController::class, 'store'])->name('admin.questions.store');
    Route::put('/admin/questions/{question}', [QuestionSwotController::class, 'update'])->name('admin.questions.update');
    Route::delete('/admin/questions/{question}', [QuestionSwotController::class, 'destroy'])->name('admin.questions.destroy');

    Route::get('/admin/strategic-issues', [StrategicIssueController::class, 'index'])->name('admin.strategic-issues.index');
    Route::post('/admin/strategic-issues', [StrategicIssueController::class, 'store'])->name('admin.strategic-issues.store');
    Route::put('/admin/strategic-issues/{strategicIssue}', [StrategicIssueController::class, 'update'])->name('admin.strategic-issues.update');
    Route::delete('/admin/strategic-issues/{strategicIssue}', [StrategicIssueController::class, 'destroy'])->name('admin.strategic-issues.destroy');

    Route::get('/admin/strategic-issues/{strategicIssue}/sub-topics', [StrategicSubTopicController::class, 'index'])->name('admin.strategic-issues.sub-topics.index');
    Route::post('/admin/strategic-issues/{strategicIssue}/sub-topics', [StrategicSubTopicController::class, 'store'])->name('admin.strategic-issues.sub-topics.store');
    Route::put('/admin/strategic-issues/{strategicIssue}/sub-topics/{subTopic}', [StrategicSubTopicController::class, 'update'])->name('admin.strategic-issues.sub-topics.update');
    Route::delete('/admin/strategic-issues/{strategicIssue}/sub-topics/{subTopic}', [StrategicSubTopicController::class, 'destroy'])->name('admin.strategic-issues.sub-topics.destroy');

    Route::get('/admin/sub-topics/{subTopic}/indicators', [StrategicIndicatorController::class, 'index'])->name('admin.strategic-sub-topics.indicators.index');
    Route::post('/admin/sub-topics/{subTopic}/indicators', [StrategicIndicatorController::class, 'store'])->name('admin.strategic-sub-topics.indicators.store');
    Route::put('/admin/sub-topics/{subTopic}/indicators/{indicator}', [StrategicIndicatorController::class, 'update'])->name('admin.strategic-sub-topics.indicators.update');
    Route::delete('/admin/sub-topics/{subTopic}/indicators/{indicator}', [StrategicIndicatorController::class, 'destroy'])->name('admin.strategic-sub-topics.indicators.destroy');
});
