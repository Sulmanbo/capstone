<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        $notifications = $user->notifications()->latest()->paginate(30);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(string $id): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $id)->update(['read_at' => now()]);

        return redirect()->back();
    }
}
