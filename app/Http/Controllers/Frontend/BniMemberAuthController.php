<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BniMemberAuthController extends Controller
{
    public function create(): View
    {
        return view('frontend.bni-member-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Thông tin đăng nhập chưa chính xác.']);
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (! $user?->hasAnyRole(['super_admin', 'bni_admin', 'bni_chapter_manager', 'bni_member'])) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages(['email' => 'Tài khoản này chưa được cấp quyền hội viên BNI.']);
        }

        return redirect()->intended(route('bni.handover'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('bni.handover');
    }
}
