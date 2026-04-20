@extends('layouts.app')

@section('title', 'Pengaturan')
@section('subtitle', 'Kelola akun dan preferensi Anda')

@section('content')
    <div style="max-width: 800px;">

        {{-- Profile Section --}}
        <div class="glass-card animate-in" style="margin-bottom: 24px;" id="profile-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-person-circle" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Informasi Profil
                </div>
            </div>
            <div class="card-body">
                {{-- Avatar Section --}}
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid var(--border-color);">
                    <div style="width: 72px; height: 72px; border-radius: 50%; background: var(--gradient-primary); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; color: white; box-shadow: 0 6px 20px rgba(230, 33, 23, 0.4);">
                        {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 2px;">{{ $user->name ?? 'Admin' }}</div>
                        <div style="font-size: 13px; color: var(--text-muted);">{{ $user->email ?? '-' }}</div>
                        <div style="display: flex; align-items: center; gap: 6px; margin-top: 6px;">
                            <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-emerald); display: inline-block;"></span>
                            <span style="font-size: 12px; color: var(--accent-emerald); font-weight: 600;">Administrator</span>
                        </div>
                    </div>
                </div>

                {{-- Profile Form --}}
                <form method="POST" action="{{ route('pengaturan.updateProfile') }}" id="form-profile">
                    @csrf
                    @method('PUT')

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="bi bi-person-fill" style="margin-right: 4px;"></i> Nama Lengkap
                            </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" required placeholder="Masukkan nama lengkap">
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">
                                <i class="bi bi-envelope-fill" style="margin-right: 4px;"></i> Email
                            </label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" required placeholder="Masukkan email">
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; padding-top: 4px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-profile">
                            <i class="bi bi-check-lg"></i> Simpan Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password Section --}}
        <div class="glass-card animate-in" style="margin-bottom: 24px;" id="password-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-shield-lock-fill" style="color: var(--accent-amber); margin-right: 8px;"></i>
                    Ubah Password
                </div>
            </div>
            <div class="card-body">
                <div style="padding: 14px 18px; background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.12); border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-info-circle-fill" style="color: var(--accent-amber); font-size: 16px;"></i>
                    <span style="font-size: 13px; color: var(--text-secondary);">Pastikan untuk menggunakan password yang kuat (minimal 8 karakter, kombinasi huruf dan angka).</span>
                </div>

                <form method="POST" action="{{ route('pengaturan.updatePassword') }}" id="form-password">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="current_password">
                            <i class="bi bi-key-fill" style="margin-right: 4px;"></i> Password Saat Ini
                        </label>
                        <div style="position: relative;">
                            <input type="password" name="current_password" id="current_password" class="form-control" required placeholder="Masukkan password saat ini" style="padding-right: 44px;">
                            <button type="button" class="toggle-password" onclick="togglePass('current_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                <i class="bi bi-lock-fill" style="margin-right: 4px;"></i> Password Baru
                            </label>
                            <div style="position: relative;">
                                <input type="password" name="password" id="password" class="form-control" required placeholder="Minimal 8 karakter" minlength="8" style="padding-right: 44px;">
                                <button type="button" class="toggle-password" onclick="togglePass('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="password_confirmation">
                                <i class="bi bi-lock-fill" style="margin-right: 4px;"></i> Konfirmasi Password
                            </label>
                            <div style="position: relative;">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Ulangi password baru" minlength="8" style="padding-right: 44px;">
                                <button type="button" class="toggle-password" onclick="togglePass('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div style="margin-bottom: 20px;" id="password-strength-container">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px;">
                            <span style="font-size: 12px; color: var(--text-muted);">Kekuatan Password</span>
                            <span style="font-size: 12px; font-weight: 600;" id="strength-text">-</span>
                        </div>
                        <div style="display: flex; gap: 4px;">
                            <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: rgba(230,33,23,0.1); transition: var(--transition);"></div>
                            <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: rgba(230,33,23,0.1); transition: var(--transition);"></div>
                            <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: rgba(230,33,23,0.1); transition: var(--transition);"></div>
                            <div class="strength-bar" style="flex: 1; height: 4px; border-radius: 2px; background: rgba(230,33,23,0.1); transition: var(--transition);"></div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; padding-top: 4px;">
                        <button type="submit" class="btn btn-primary" id="btn-save-password">
                            <i class="bi bi-shield-check"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Account Info Card --}}
        <div class="glass-card animate-in" id="account-info-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-info-circle-fill" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Informasi Akun
                </div>
            </div>
            <div class="card-body">
                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-hash" style="margin-right: 4px;"></i> ID Akun
                        </div>
                        <div class="detail-item-value">{{ $user->id ?? '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-shield-fill" style="margin-right: 4px;"></i> Role
                        </div>
                        <div class="detail-item-value">
                            <span class="badge badge-approved">Administrator</span>
                        </div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-calendar-plus" style="margin-right: 4px;"></i> Tanggal Daftar
                        </div>
                        <div class="detail-item-value">{{ $user->created_at ? $user->created_at->format('d F Y, H:i') : '-' }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-item-label">
                            <i class="bi bi-arrow-repeat" style="margin-right: 4px;"></i> Terakhir Diperbarui
                        </div>
                        <div class="detail-item-value">{{ $user->updated_at ? $user->updated_at->format('d F Y, H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
    function togglePass(fieldId, btn) {
        const field = document.getElementById(fieldId);
        const icon = btn.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.getElementById('strength-text');

    const strengthConfig = [
        { label: 'Lemah', color: '#E62117', count: 1 },
        { label: 'Cukup', color: '#f59e0b', count: 2 },
        { label: 'Baik', color: '#0DE5FF', count: 3 },
        { label: 'Kuat', color: '#10b981', count: 4 }
    ];

    passwordInput.addEventListener('input', function() {
        const val = this.value;
        let score = 0;

        if (val.length >= 8) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^a-zA-Z0-9]/.test(val)) score++;

        // Reset
        strengthBars.forEach(bar => bar.style.background = 'rgba(230,33,23,0.1)');
        strengthText.textContent = '-';
        strengthText.style.color = 'var(--text-muted)';

        if (val.length > 0 && score > 0) {
            const config = strengthConfig[score - 1];
            for (let i = 0; i < config.count; i++) {
                strengthBars[i].style.background = config.color;
            }
            strengthText.textContent = config.label;
            strengthText.style.color = config.color;
        }
    });
</script>
@endsection
