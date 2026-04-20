<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login - Sistem Pengajuan ATK">
    <title>Login - Pengajuan ATK</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body>
    {{-- Floating Particles --}}
    @for($i = 0; $i < 15; $i++)
        <div class="particle" style="left: {{ rand(5, 95) }}%; animation-duration: {{ rand(8, 20) }}s; animation-delay: {{ rand(0, 10) }}s; width: {{ rand(2, 5) }}px; height: {{ rand(2, 5) }}px; background: rgba(230, {{ rand(33, 80) }}, {{ rand(23, 60) }}, {{ rand(15, 35) / 100 }});"></div>
    @endfor

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="bi bi-box-seam-fill"></i>
                </div>
                <h1>Sistem Pengajuan ATK</h1>
                <p>Masuk ke akun Anda</p>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('forgot_success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill"></i> {{ session('forgot_success') }}
                </div>
            @endif

            @if(session('forgot_info'))
                <div class="alert alert-info">
                    <i class="bi bi-info-circle-fill"></i> {{ session('forgot_info') }}
                </div>
            @endif

            @if($errors->has('email'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('email') }}
                </div>
            @endif

            @if($errors->has('forgot_email'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first('forgot_email') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="form-login">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="remember-row">
                    <label class="remember-check">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ingat saya</span>
                    </label>
                    <button type="button" class="forgot-link" onclick="document.getElementById('forgotModal').classList.add('show')" id="btn-forgot">
                        Lupa password?
                    </button>
                </div>

                <button type="submit" class="btn-primary" id="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <div class="auth-footer">
                Hubungi administrator untuk membuat akun baru.
            </div>
        </div>
    </div>

    {{-- Forgot Password Modal --}}
    <div class="modal-overlay" id="forgotModal">
        <div class="modal-box">
            <h3><i class="bi bi-key-fill" style="color: #E62117;"></i> Lupa Password</h3>
            <p>Masukkan email akun Anda. Permintaan reset password akan dikirim ke administrator untuk diproses.</p>

            <form method="POST" action="{{ route('forgot.password') }}" id="form-forgot">
                @csrf
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope-fill"></i>
                        <input type="email" name="email" class="form-control" placeholder="nama@email.com" required id="forgot-email">
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-ghost" onclick="document.getElementById('forgotModal').classList.remove('show')">Batal</button>
                    <button type="submit" class="btn-submit"><i class="bi bi-send-fill"></i> Kirim Permintaan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('forgotModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    </script>
</body>
</html>
