<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\QuestionSwot;
use App\Models\SwotCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'userCount' => User::count(),
            'categoryCount' => SwotCategory::count(),
            'questionCount' => QuestionSwot::count(),
        ]);
    }

    public function users()
    {
        $users = User::orderBy('fullname')->get();

        return view('admin.users', [
            'users' => $users,
        ]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate([
            'misid' => 'required|string|max:255|unique:user,misid',
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'facultyid' => 'nullable|string|max:255',
            'faculty_name' => 'nullable|string|max:255',
            'role' => 'required|in:admin,user',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $user = User::create($data);

        ActivityLogger::log('create_user', $user->id, 'เพิ่มผู้ใช้งาน ' . $user->fullname);

        return redirect()->route('admin.users.index')->with('success', 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate([
            'misid' => 'required|string|max:255|unique:user,misid,' . $user->id,
            'username' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'facultyid' => 'nullable|string|max:255',
            'faculty_name' => 'nullable|string|max:255',
            'role' => 'required|in:admin,user',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $user->update($data);

        ActivityLogger::log('update_user', $user->id, 'แก้ไขผู้ใช้งาน ' . $user->fullname);

        return redirect()->route('admin.users.index')->with('success', 'แก้ไขข้อมูลผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Session::get('user_id')) {
            return redirect()->route('admin.users.index')->with('error', 'ไม่สามารถลบบัญชีของตนเองได้');
        }

        $name = $user->fullname;
        $user->delete();

        ActivityLogger::log('delete_user', null, 'ลบผู้ใช้งาน ' . $name);

        return redirect()->route('admin.users.index')->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}
