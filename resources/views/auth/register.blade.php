<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar Akun - Sistem Pengajuan ATK">
    <title>Daftar - Pengajuan ATK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0a0e1a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f1f5f9;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 60% 80%, rgba(6, 182, 212, 0.06) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .auth-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            padding: 20px;
        }

        .auth-card {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
            padding: 36px 36px;
            animation: slideUp 0.6s ease;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: white;
            margin-bottom: 14px;
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .auth-brand h1 {
            font-size: 22px;
            font-weight: 800;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-brand p {
            font-size: 13px;
            color: #64748b;
            margin-top: 4px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: 12px;
            color: #f1f5f9;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .form-control::placeholder { color: #475569; }

        .form-error {
            font-size: 12px;
            color: #fb7185;
            margin-top: 4px;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            font-size: 16px;
        }

        .input-icon-wrapper input,
        .input-icon-wrapper select {
            padding-left: 42px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .btn-primary {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary:hover {
            box-shadow: 0 6px 25px rgba(99, 102, 241, 0.5);
            transform: translateY(-2px);
        }

        .auth-footer {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #64748b;
        }

        .auth-footer a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-footer a:hover { color: #a5b4fc; }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-danger {
            background: rgba(244, 63, 94, 0.1);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.2);
        }

        .dept-info {
            padding: 12px 16px;
            background: rgba(99, 102, 241, 0.06);
            border: 1px solid rgba(99, 102, 241, 0.12);
            border-radius: 12px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #94a3b8;
        }

        .dept-info i { color: #6366f1; font-size: 14px; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .particle {
            position: fixed;
            width: 4px; height: 4px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.3);
            animation: float linear infinite;
            pointer-events: none;
        }

        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        @media (max-width: 500px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @for($i = 0; $i < 15; $i++)
        <div class="particle" style="left: {{ rand(5, 95) }}%; animation-duration: {{ rand(8, 20) }}s; animation-delay: {{ rand(0, 10) }}s; width: {{ rand(2, 5) }}px; height: {{ rand(2, 5) }}px; background: rgba({{ rand(99, 139) }}, {{ rand(92, 102) }}, {{ rand(212, 246) }}, {{ rand(15, 35) / 100 }});"></div>
    @endfor

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h1>Buat Akun Baru</h1>
                <p>Daftar untuk mengajukan ATK</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="dept-info">
                <i class="bi bi-info-circle-fill"></i>
                Pilih departemen Anda. Anda hanya dapat melihat pengajuan dari departemen yang sama.
            </div>

            <form method="POST" action="{{ route('register') }}" id="form-register">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap <span style="color: #f43f5e;">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-person-fill"></i>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nama lengkap" value="{{ old('name') }}" required autofocus>
                        </div>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Email <span style="color: #f43f5e;">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-envelope-fill"></i>
                            <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="departemen">Departemen <span style="color: #f43f5e;">*</span></label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-building"></i>
                        <select name="departemen" id="departemen" class="form-control" required>
                            <option value="">Pilih Departemen Anda</option>
                            <option value="IT" {{ old('departemen') === 'IT' ? 'selected' : '' }}>IT</option>
                            <option value="HRD" {{ old('departemen') === 'HRD' ? 'selected' : '' }}>HRD</option>
                            <option value="Keuangan" {{ old('departemen') === 'Keuangan' ? 'selected' : '' }}>Keuangan</option>
                            <option value="Marketing" {{ old('departemen') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
                            <option value="Operasional" {{ old('departemen') === 'Operasional' ? 'selected' : '' }}>Operasional</option>
                            <option value="Umum" {{ old('departemen') === 'Umum' ? 'selected' : '' }}>Umum</option>
                        </select>
                    </div>
                    @error('departemen') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password <span style="color: #f43f5e;">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Min. 8 karakter" required minlength="8">
                        </div>
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password <span style="color: #f43f5e;">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-lock-fill"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password" required minlength="8">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="btn-register" style="margin-top: 4px;">
                    <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                </button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
