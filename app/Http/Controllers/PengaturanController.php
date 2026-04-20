<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PengaturanController extends Controller
{
    /**
     * Halaman pengaturan akun.
     */
    public function index()
    {
        $user = Auth::user();

        return view('pengaturan.index', compact('user'));
    }

    /**
     * Update profil (nama & email).
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('pengaturan.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('pengaturan.index')
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->with('tab', 'password');
        }

        $user->update([
            'password' => $request->password,
        ]);

        return redirect()->route('pengaturan.index')->with('success', 'Password berhasil diubah!');
    }
}
