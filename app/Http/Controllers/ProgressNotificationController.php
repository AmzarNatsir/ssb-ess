<?php

namespace App\Http\Controllers;

use App\Helpers\ProgressNotificationService;
use Illuminate\Http\Request;

class ProgressNotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        ProgressNotificationService::markAllAsReadForCurrentUser();

        return redirect()->back()->with('success', 'Semua notifikasi progress telah ditandai sudah dibaca.');
    }

    public function markRead(Request $request)
    {
        $itemKey = (string) $request->query('item_key', '');
        $redirectUrl = (string) $request->query('redirect_url', '');

        if (!empty($itemKey)) {
            ProgressNotificationService::markItemAsReadForCurrentUser($itemKey);
        }

        if (!empty($redirectUrl)) {
            return redirect()->to($redirectUrl);
        }

        return redirect()->route('home');
    }
}
