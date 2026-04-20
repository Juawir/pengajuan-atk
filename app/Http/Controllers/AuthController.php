<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ResetPasswordRequest;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/')->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses permintaan reset password dari halaman login.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Cek apakah email terdaftar
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['forgot_email' => 'Email tidak ditemukan dalam sistem.'])->withInput();
        }

        // Cek apakah sudah ada request pending
        $existingRequest = ResetPasswordRequest::where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return back()->with('forgot_info', 'Permintaan reset password Anda sudah dikirim sebelumnya. Silakan hubungi admin.');
        }

        // Buat request baru
        ResetPasswordRequest::create([
            'email'  => $request->email,
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke semua admin
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id'      => $admin->id,
                'type'         => 'pengajuan_baru',
                'title'        => 'Permintaan Reset Password',
                'message'      => 'User ' . $user->name . ' (' . $request->email . ') meminta reset password.',
                'pengajuan_id' => null,
            ]);
        }

        return back()->with('forgot_success', 'Permintaan reset password telah dikirim ke admin. Silakan hubungi admin untuk mendapatkan password baru.');
    }

    /**
     * Tampilkan halaman registrasi akun (Admin Only).
     */
    public function showRegistrasi()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        $resetRequests = ResetPasswordRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('registrasi.index', compact('users', 'resetRequests'));
    }

    /**
     * Proses registrasi akun oleh admin.
     */
    public function registerUser(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'departemen' => 'required|string|max:255',
        ]);

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => $request->password,
            'role'       => 'user', // default role user
            'departemen' => $request->departemen,
        ]);

        return redirect()->route('registrasi.index')->with('success', 'Akun berhasil didaftarkan untuk departemen ' . $request->departemen . '!');
    }

    /**
     * Admin reset password user.
     */
    public function resetUserPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $user->update([
            'password' => $request->new_password,
        ]);

        // Tandai semua request pending milik user ini sebagai resolved
        ResetPasswordRequest::where('email', $user->email)
            ->where('status', 'pending')
            ->update([
                'status'      => 'resolved',
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);

        return redirect()->route('registrasi.index')->with('success', 'Password ' . $user->name . ' berhasil direset!');
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
