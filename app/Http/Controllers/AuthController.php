<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login()
    {
        if (Session::get('logged_in')) {
            return redirect()->route(Session::get('role') === 'admin' ? 'admin.index' : 'home.index');
        }

        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'กรุณากรอกชื่อผู้ใช้',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        try {
            $response = Http::timeout(10)->asForm()->post(env('RMU_AUTHEN_URL'), [
                'authen_username' => $username,
                'authen_password' => $password,
                'authen_system'   => env('RMU_AUTHEN_SYSTEM'),
                'authen_token'    => env('RMU_AUTHEN_TOKEN'),
            ]);

            if ($response->json('statusCode') === 200) {
                $data    = $response->json();
                $profile = $data['profile'];

                if ($profile['USERTYPE'] === "0") {
                    return redirect()->route('login')
                        ->with('error', 'ท่านไม่มีสิทธิ์เข้าใช้งานระบบ');
                }

                $user = User::where('misid', $profile['MISID'])->first();

                if (! $user || ! $user->is_active) {
                    return redirect()->route('login')
                        ->withInput($request->only('username'))
                        ->with('error', 'ไม่พบผู้ใช้งานนี้ในระบบ หรือบัญชีถูกระงับการใช้งาน');
                }

                Session::put('logged_in',    true);
                Session::put('user',         $username);
                Session::put('user_id',      $user->id);
                Session::put('USERID',       $profile['MISID']);
                Session::put('MISID',        $profile['MISID']);
                Session::put('USERCODE',     $profile['USERCODE'] ?? '');
                Session::put('FACULTYID',    $profile['FACULTYID'] ?? '');
                Session::put('USERFULLNAME', $user->fullname);
                Session::put('role',         $user->role);
                Session::put('ROLE',         $user->role);

                ActivityLogger::log('login', $user->id, 'เข้าสู่ระบบในฐานะ ' . ($user->role === 'admin' ? 'Admin' : 'User'));

                return redirect()->route($user->role === 'admin' ? 'admin.index' : 'home.index');
            }

            return redirect()->route('login')
                ->withInput($request->only('username'))
                ->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');

        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withInput($request->only('username'))
                ->with('error', 'เกิดข้อผิดพลาด: ไม่สามารถเชื่อมต่อระบบได้');
        }
    }

    public function logout()
    {
        ActivityLogger::log('logout', Session::get('user_id'), 'ออกจากระบบ');
        Session::flush();
        return redirect()->route('login');
    }
}
