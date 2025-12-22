<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot impersonate another admin.');
        }

        session()->put('impersonate_by', auth()->id());
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "You are now impersonating {$user->name}");
    }

    public function stopImpersonating()
    {
        if (!session()->has('impersonate_by')) {
            return redirect()->route('dashboard');
        }

        $originalUserId = session()->get('impersonate_by');
        session()->forget('impersonate_by');

        Auth::loginUsingId($originalUserId);

        return redirect()->route('admin.users.index')->with('success', 'Welcome back!');
    }
}
