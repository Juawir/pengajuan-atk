<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pengajuan ATK - Dashboard Modern untuk Manajemen Alat Tulis Kantor">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Pengajuan ATK</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    {{-- App CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="bi bi-box-seam-fill"></i>
            </div>
            <div>
                <div class="sidebar-brand-text">Sistem Pengajuan ATK</div>
                <div class="sidebar-brand-sub">Management Portal</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" id="nav-dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                Dashboard
            </a>
            <a href="{{ route('pengajuan.index') }}" class="nav-item {{ request()->routeIs('pengajuan.index') || request()->routeIs('pengajuan.show') ? 'active' : '' }}" id="nav-pengajuan">
                <i class="bi bi-file-earmark-text-fill"></i>
                Data Pengajuan
            </a>
            <a href="{{ route('pengajuan.create') }}" class="nav-item {{ request()->routeIs('pengajuan.create') ? 'active' : '' }}" id="nav-tambah">
                <i class="bi bi-plus-circle-fill"></i>
                Buat Pengajuan
            </a>
            <a href="{{ route('pinjaman.index') }}" class="nav-item {{ request()->routeIs('pinjaman.*') ? 'active' : '' }}" id="nav-pinjaman">
                <i class="bi bi-arrow-left-right"></i>
                Pinjaman Barang
            </a>
            <div class="nav-label" style="margin-top: 16px;">Informasi</div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}" id="nav-laporan">
                    <i class="bi bi-bar-chart-line-fill"></i>
                    Laporan
                </a>
            @endif
            <a href="{{ route('pengaturan.index') }}" class="nav-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}" id="nav-pengaturan">
                <i class="bi bi-gear-fill"></i>
                Pengaturan
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('registrasi.index') }}" class="nav-item {{ request()->routeIs('registrasi.*') ? 'active' : '' }}" id="nav-registrasi">
                    <i class="bi bi-person-plus-fill"></i>
                    Registrasi Akun
                </a>
            @endif

            {{-- Theme Toggle --}}
            <div style="padding: 12px 14px; margin-top: 8px;">
                <button class="theme-toggle-btn" id="btn-theme-toggle" onclick="toggleTheme()" title="Ubah Tema">
                    <div class="theme-toggle-track">
                        <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
                        <i class="bi bi-sun-fill theme-icon-light"></i>
                        <div class="theme-toggle-thumb"></div>
                    </div>
                    <span class="theme-toggle-label" id="theme-label">Dark Mode</span>
                </button>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-info">
                <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div style="flex: 1;">
                    <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                    <div class="sidebar-user-role">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : auth()->user()->departemen }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" title="Logout" class="sidebar-logout-btn" id="btn-logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="main-content">
        {{-- Top Bar --}}
        <header class="topbar">
            <div class="topbar-left-wrapper">
                <button class="sidebar-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')" id="btn-toggle-sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <div class="topbar-left">
                    <h1>@yield('title', 'Dashboard')</h1>
                    <p>@yield('subtitle', 'Selamat datang di Sistem Pengajuan ATK')</p>
                </div>
            </div>
            <div class="topbar-right">
                <div class="notif-wrapper" id="notif-wrapper">
                    <button class="topbar-btn" id="btn-notifications" title="Notifikasi" onclick="toggleNotifDropdown()">
                        <i class="bi bi-bell-fill"></i>
                        <span class="topbar-badge" id="notif-badge" style="display: none;">0</span>
                    </button>
                    <div class="notif-dropdown" id="notif-dropdown">
                        <div class="notif-dropdown-header">
                            <span class="notif-dropdown-title">
                                <i class="bi bi-bell-fill" style="margin-right: 6px;"></i> Notifikasi
                            </span>
                            <button class="notif-mark-all" id="btn-mark-all-read" onclick="markAllAsRead()" title="Tandai semua sudah dibaca">
                                <i class="bi bi-check-all"></i> Tandai Dibaca
                            </button>
                        </div>
                        <div class="notif-dropdown-body" id="notif-list">
                            <div class="notif-empty">
                                <i class="bi bi-bell-slash" style="font-size: 28px; color: var(--text-muted);"></i>
                                <p>Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <div class="page-body">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success" id="alert-success">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" id="alert-error">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script>
        // ===== THEME SYSTEM =====
        function getTheme() {
            return localStorage.getItem('atk-theme') || 'dark';
        }

        function applyTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            const label = document.getElementById('theme-label');
            const btn = document.getElementById('btn-theme-toggle');
            if (label) label.textContent = theme === 'dark' ? 'Dark Mode' : 'Light Mode';
            if (btn) {
                if (theme === 'light') {
                    btn.classList.add('light');
                } else {
                    btn.classList.remove('light');
                }
            }
        }

        function toggleTheme() {
            const current = getTheme();
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('atk-theme', next);
            applyTheme(next);
        }

        // Apply saved theme immediately
        applyTheme(getTheme());

        // Auto-hide alerts after 4 seconds
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 400);
            }, 4000);
        });

        // Close sidebar on overlay click (mobile) & close notif dropdown
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('btn-toggle-sidebar');
            if (window.innerWidth <= 768 && sidebar.classList.contains('open')) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            }

            const notifWrapper = document.getElementById('notif-wrapper');
            const notifDropdown = document.getElementById('notif-dropdown');
            if (notifWrapper && notifDropdown && !notifWrapper.contains(e.target)) {
                notifDropdown.classList.remove('show');
            }
        });

        // ===== NOTIFICATION SYSTEM =====
        const csrfToken = '{{ csrf_token() }}';

        function toggleNotifDropdown() {
            const dropdown = document.getElementById('notif-dropdown');
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                fetchNotifications();
            }
        }

        function fetchNotifications() {
            fetch('/notifikasi', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                renderNotifications(data.notifikasis);
                updateBadge(data.unread_count);
            })
            .catch(err => console.error('Notif fetch error:', err));
        }

        function updateBadge(count) {
            const badge = document.getElementById('notif-badge');
            if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }

        function timeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const diffMs = now - date;
            const diffMin = Math.floor(diffMs / 60000);
            const diffHr = Math.floor(diffMin / 60);
            const diffDay = Math.floor(diffHr / 24);

            if (diffMin < 1) return 'Baru saja';
            if (diffMin < 60) return diffMin + ' menit lalu';
            if (diffHr < 24) return diffHr + ' jam lalu';
            if (diffDay < 7) return diffDay + ' hari lalu';
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function getNotifIcon(type) {
            const icons = {
                'pengajuan_baru': 'bi-file-earmark-plus-fill',
                'status_disetujui': 'bi-check-circle-fill',
                'status_ditolak': 'bi-x-circle-fill',
                'status_pending': 'bi-hourglass-split'
            };
            return icons[type] || 'bi-bell-fill';
        }

        function renderNotifications(notifs) {
            const container = document.getElementById('notif-list');

            if (!notifs || notifs.length === 0) {
                container.innerHTML = `
                    <div class="notif-empty">
                        <i class="bi bi-bell-slash" style="font-size: 28px; color: var(--text-muted);"></i>
                        <p>Tidak ada notifikasi</p>
                    </div>`;
                return;
            }

            container.innerHTML = notifs.map(n => `
                <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="markAsRead(${n.id}, ${n.pengajuan_id || 'null'})" data-id="${n.id}">
                    <div class="notif-icon ${n.type}">
                        <i class="bi ${getNotifIcon(n.type)}"></i>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">${n.title}</div>
                        <div class="notif-message">${n.message}</div>
                        <div class="notif-time">${timeAgo(n.created_at)}</div>
                    </div>
                </div>
            `).join('');
        }

        function markAsRead(id, pengajuanId) {
            fetch('/notifikasi/' + id + '/read', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).then(() => {
                const item = document.querySelector(`.notif-item[data-id="${id}"]`);
                if (item) item.classList.remove('unread');
                fetchNotifications();

                if (pengajuanId) {
                    window.location.href = '/pengajuan/' + pengajuanId;
                }
            });
        }

        function markAllAsRead() {
            fetch('/notifikasi/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            }).then(() => {
                fetchNotifications();
            });
        }

        // Initial badge load & auto-refresh every 30 seconds
        fetchNotifications();
        setInterval(() => {
            const dropdown = document.getElementById('notif-dropdown');
            if (!dropdown.classList.contains('show')) {
                fetch('/notifikasi', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => updateBadge(data.unread_count))
                .catch(() => {});
            }
        }, 30000);
    </script>

    @yield('scripts')
</body>
</html>
