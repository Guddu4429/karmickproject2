<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function show(Request $request, $type = null)
    {
        // If no type is provided, redirect to login selection page
        if (!$type) {
            return redirect()->route('login');
        }

        // Validate login type
        $validTypes = ['guardian', 'teacher', 'admin'];
        if (!in_array($type, $validTypes)) {
            return redirect()->route('login')->with('error', 'Invalid login type.');
        }

        return view('auth.login-form', compact('type'));
    }

    public function login(Request $request, $type)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Determine role based on login type
        $roleMap = [
            'guardian' => 'Guardian',  // Students login through guardians
            'teacher' => 'Faculty',
            'admin' => 'Principal',
        ];

        $expectedRole = $roleMap[$type] ?? null;

        if (!$expectedRole) {
            return back()->withErrors(['email' => 'Invalid login type.'])->withInput();
        }

        // Get the role ID
        $roleId = DB::table('roles')->where('name', $expectedRole)->value('id');

        if (!$roleId) {
            return back()->withErrors(['email' => 'Role not found.'])->withInput();
        }

        // Attempt authentication
        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput();
        }

        $user = Auth::user();

        // Verify the user has the correct role
        if ($user->role_id != $roleId) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors([
                'email' => 'Invalid credentials for this login type.',
            ])->withInput();
        }

        $request->session()->regenerate();

        // For guardians, check if they have multiple students
        if ($expectedRole === 'Guardian') {
            $guardianId = DB::table('guardians')->where('user_id', $user->id)->value('id');
            
            if ($guardianId) {
                $studentCount = DB::table('students')
                    ->where('guardian_id', $guardianId)
                    ->count();

                // If multiple students, redirect to selection page
                if ($studentCount > 1) {
                    $request->session()->forget('active_student_id');
                    return redirect()->route('guardian.children');
                } 
                // If single student, redirect to their dashboard
                elseif ($studentCount === 1) {
                    $student = DB::table('students')
                        ->where('guardian_id', $guardianId)
                        ->first();
                    if ($student) {
                        $request->session()->put('active_student_id', $student->id);
                        return redirect()->route('guardian.student.dashboard', ['student' => $student->id]);
                    }
                }
                // If no students, redirect to children page anyway (will show message)
                return redirect()->route('guardian.children');
            } else {
                // Guardian user but no guardian record found
                return redirect()->route('guardian.children');
            }
        }

        // For other roles (Principal and Faculty), redirect to main dashboard
        return redirect('/');
    }
}
