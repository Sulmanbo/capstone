<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::sendResetLink(['email' => $request->input('email')]);

        // Always return the same message whether email exists or not
        // (prevents user enumeration attacks)
        return back()->with('status', 'If that email is registered, a reset link has been sent.');
    }
}