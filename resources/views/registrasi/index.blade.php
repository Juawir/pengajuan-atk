@extends('layouts.app')

@section('title', 'Registrasi Akun')
@section('subtitle', 'Daftarkan akun departemen baru & kelola password')

@section('content')
    <div style="max-width: 720px;">

        {{-- Reset Password Requests --}}
        @if(isset($resetRequests) && $resetRequests->count() > 0)
            <div class="glass-card animate-in" style="margin-bottom: 24px; border: 1px solid rgba(230, 33, 23, 0.25);" id="reset-requests-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-key-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                        Permintaan Reset Password
                        <span style="background: var(--accent-primary); color: white; font-size: 11px; padding: 2px 8px; border-radius: 20px; margin-left: 8px;">{{ $resetRequests->count() }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @foreach($resetRequests as $req)
                        <div style="padding: 14px 18px; background: rgba(230, 33, 23, 0.06); border: 1px solid rgba(230, 33, 23, 0.12); border-radius: var(--radius-md); margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">{{ $req->email }}</div>
                                <div style="font-size: 12px; color: var(--text-muted);">Diminta {{ $req->created_at->diffForHumans() }}</div>
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <button type="button" class="btn btn-primary btn-sm" onclick="openResetModal('{{ $req->email }}')" id="btn-reset-{{ $loop->index }}">
                                    <i class="bi bi-key"></i> Reset Password
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Register Form --}}
        <div class="glass-card animate-in" id="register-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-person-plus-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Daftarkan Akun Departemen Baru
                </div>
            </div>
            <div class="card-body">
                {{-- Info Box --}}
                <div style="padding: 14px 18px; background: rgba(230, 33, 23, 0.06); border: 1px solid rgba(230, 33, 23, 0.12); border-radius: var(--radius-md); margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-info-circle-fill" style="color: var(--accent-primary); font-size: 16px;"></i>
                    <span style="font-size: 13px; color: var(--text-secondary);">Sebagai admin, Anda dapat mendaftarkan akun baru untuk setiap departemen. User yang didaftarkan hanya dapat melihat pengajuan dari departemen mereka.</span>
                </div>

                <form method="POST" action="{{ route('registrasi.store') }}" id="form-register-user">
                    @csrf

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                <i class="bi bi-person-fill" style="margin-right: 4px;"></i> Nama Lengkap <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama lengkap" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="email">
                                <i class="bi bi-envelope-fill" style="margin-right: 4px;"></i> Email <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="departemen">
                            <i class="bi bi-building" style="margin-right: 4px;"></i> Departemen <span style="color: var(--accent-primary);">*</span>
                        </label>
                        <div class="combobox-wrapper">
                            <input type="text" name="departemen" id="departemen" class="form-control" list="departemen-list" placeholder="Pilih atau ketik departemen" value="{{ old('departemen') }}" required autocomplete="off">
                            <datalist id="departemen-list">
                                <option value="IT">
                                <option value="HRD">
                                <option value="Keuangan">
                                <option value="Marketing">
                                <option value="Operasional">
                                <option value="Umum">
                            </datalist>
                        </div>
                        @error('departemen')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="password">
                                <i class="bi bi-lock-fill" style="margin-right: 4px;"></i> Password <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <div style="position: relative;">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8" style="padding-right: 44px;">
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
                                <i class="bi bi-lock-fill" style="margin-right: 4px;"></i> Konfirmasi Password <span style="color: var(--accent-primary);">*</span>
                            </label>
                            <div style="position: relative;">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required minlength="8" style="padding-right: 44px;">
                                <button type="button" class="toggle-password" onclick="togglePass('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; padding-top: 8px;">
                        <button type="submit" class="btn btn-primary" id="btn-register-user">
                            <i class="bi bi-person-plus-fill"></i> Daftarkan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Registered Users Table --}}
        @if(isset($users) && $users->count() > 0)
            <div class="glass-card animate-in" style="margin-top: 24px;" id="users-table-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="bi bi-people-fill" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                        Daftar Akun Terdaftar
                    </div>
                    <span style="font-size: 13px; color: var(--text-muted);">{{ $users->count() }} akun</span>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="table-users">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Departemen</th>
                                <th>Role</th>
                                <th>Terdaftar</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $i => $u)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $u->name }}</td>
                                    <td style="color: var(--text-secondary);">{{ $u->email }}</td>
                                    <td>{{ $u->departemen ?? '-' }}</td>
                                    <td>
                                        @if($u->isAdmin())
                                            <span class="badge badge-approved">Admin</span>
                                        @else
                                            <span class="badge badge-pending">User</span>
                                        @endif
                                    </td>
                                    <td style="color: var(--text-secondary);">{{ $u->created_at->format('d M Y') }}</td>
                                    <td style="text-align: center;">
                                        @if(!$u->isAdmin())
                                            <button type="button" class="btn btn-warning btn-sm" onclick="openResetModal('{{ $u->email }}')" title="Reset Password" id="btn-reset-user-{{ $u->id }}">
                                                <i class="bi bi-key"></i>
                                            </button>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Reset Password Modal --}}
    <div class="modal-overlay" id="resetModal">
        <div class="modal-box">
            <div class="modal-title">
                <i class="bi bi-key-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                Reset Password User
            </div>
            <div class="modal-text" style="margin-bottom: 16px;">
                Reset password untuk: <strong id="reset-email-display" style="color: var(--accent-cyan);"></strong>
            </div>
            <form method="POST" id="form-reset-password" action="">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label class="form-label" for="new_password">Password Baru</label>
                    <div style="position: relative;">
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8" style="padding-right: 44px;">
                        <button type="button" onclick="togglePass('new_password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 16px;">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-ghost" onclick="closeResetModal()" id="btn-cancel-reset">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-confirm-reset">
                        <i class="bi bi-check-lg"></i> Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
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

    // User data for reset modal
    const usersData = @json($users->map(fn($u) => ['id' => $u->id, 'email' => $u->email]));

    function openResetModal(email) {
        const user = usersData.find(u => u.email === email);
        if (!user) return;

        document.getElementById('reset-email-display').textContent = email;
        document.getElementById('form-reset-password').action = '/registrasi/' + user.id + '/reset-password';
        document.getElementById('new_password').value = '';
        document.getElementById('resetModal').classList.add('show');
    }

    function closeResetModal() {
        document.getElementById('resetModal').classList.remove('show');
    }

    document.getElementById('resetModal').addEventListener('click', function(e) {
        if (e.target === this) closeResetModal();
    });
</script>
@endsection
