<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->authenticated($request, Auth::user());
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    protected function authenticated(Request $request, $user)
    {
        Cache::flush();
        if ($user->role_id == '1') // 1 = Admin
        {
            return redirect()->route('admin.dashboard')->with('status', 'Welcome to your dashboard');
        }
        if ($user->role_id == '2') // 2 = Mentor    
        {
            return redirect()->route('mentor.dashboard')->with('status', 'Welcome to your dashboard');
        }
        if ($user->role_id == '3') // 3 = Peserta
        {
            return redirect()->route('peserta.dashboard')->with('status', 'Welcome to your dashboard');
        }
        if ($user->role_id == '4') // 4 = vendor
        {
            return redirect()->route('vendor.dashboard')->with('status', 'Welcome to your dashboard');
        }
        if ($user->role_id == '0') // 0 = User Biasa
        {
            return redirect()->route('user.dashboard')->with('status', 'Welcome to your dashboard');
        }
        return redirect('/')->with('status', 'Logged in successfully');
    }
}
