<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifikasi;

class NotifikasiController extends Controller
{
    /**
     * Ambil notifikasi user yang login (untuk AJAX).
     */
    public function index()
    {
        $notifikasis = Notifikasi::where('user_id', auth()->id())
            ->latest()
            ->take(20)
            ->get();

        $unreadCount = Notifikasi::where('user_id', auth()->id())
            ->unread()
            ->count();

        return response()->json([
            'notifikasis' => $notifikasis,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Notifikasi $notifikasi)
    {
        // Pastikan notifikasi milik user yang login
        if ($notifikasi->user_id !== auth()->id()) {
            abort(403);
        }

        $notifikasi->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        Notifikasi::where('user_id', auth()->id())
            ->unread()
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
