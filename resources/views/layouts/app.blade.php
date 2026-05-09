<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Prudent')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <style>
        :root {
            --bg-dark:   #0F0F0F;
            --bg-card:   #1A1A1A;
            --bg-input:  #242424;
            --amber:     #F59E0B;
            --text-main: #F5F0E8;
            --text-muted:#6B6B6B;
            --border:    #2A2A2A;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            background: var(--bg-dark);
            color: var(--text-main);
            font-family: 'DM Sans', sans-serif;
            min-height: 100dvh;
        }
        body::-webkit-scrollbar { width: 6px; }
        body::-webkit-scrollbar-track { background: var(--bg-dark); }
        body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 3px; }
        * { -webkit-tap-highlight-color: transparent; }
        .font-display { font-family: 'DM Serif Display', serif; }
        .input-field {
            background: var(--bg-input); border: 1px solid var(--border);
            color: var(--text-main); border-radius: 12px; padding: 14px 16px;
            width: 100%; font-family: 'DM Sans', sans-serif; font-size: 15px;
            transition: border-color 0.2s; outline: none;
        }
        .input-field:focus { border-color: var(--amber); box-shadow: 0 0 0 3px rgba(245,158,11,0.12); }
        .input-field::placeholder { color: var(--text-muted); }
        textarea.input-field { resize: none; }
        select.input-field {
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B6B6B' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px;
        }
        input[type=date]::-webkit-calendar-picker-indicator { filter: invert(0.4); cursor: pointer; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
        input[type=number] { -moz-appearance: textfield; }
        .btn-primary {
            background: var(--amber); color: #0F0F0F; border: none;
            border-radius: 12px; padding: 15px; width: 100%;
            font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 15px;
            cursor: pointer; transition: background 0.2s, transform 0.1s;
        }
        .btn-primary:hover { background: #FBBF24; box-shadow: 0 4px 20px rgba(245,158,11,0.3); }
        .btn-primary:active { transform: scale(0.98); }
        .label-field {
            font-size: 12px; font-weight: 500; color: var(--text-muted);
            letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 8px;
        }
        main { animation: pageFadeIn 0.25s ease forwards; }
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ===== DESKTOP LAYOUT ===== */
        .app-wrapper {
            display: flex;
            min-height: 100dvh;
        }

        /* Sidebar — desktop only */
        .sidebar {
            display: none;
        }

        /* Main content area */
        .main-content {
            flex: 1;
            min-width: 0;
            padding-bottom: 80px; /* space for bottom nav mobile */
        }

        /* Bottom nav — mobile only */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(26,26,26,0.97);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            padding: 10px 0 max(20px, env(safe-area-inset-bottom));
            z-index: 100;
        }

        /* ===== DESKTOP (>= 1024px) ===== */
        @media (min-width: 1024px) {
            .app-wrapper {
                max-width: 1280px;
                margin: 0 auto;
            }

            /* Show sidebar */
            .sidebar {
                display: flex;
                flex-direction: column;
                width: 260px;
                min-height: 100dvh;
                background: var(--bg-card);
                border-right: 1px solid var(--border);
                padding: 32px 20px;
                position: sticky;
                top: 0;
                height: 100dvh;
                overflow-y: auto;
                flex-shrink: 0;
            }

            /* Hide bottom nav */
            .bottom-nav { display: none !important; }

            /* Main content no bottom padding */
            .main-content { padding-bottom: 40px; }

            /* Content max width on desktop */
            .content-inner {
                max-width: 720px;
                margin: 0 auto;
                padding: 0 32px;
            }
        }

        /* Tablet (768px - 1023px) */
        @media (min-width: 768px) and (max-width: 1023px) {
            .sidebar { display: none; }
            .main-content { padding-bottom: 80px; }
            .content-inner {
                max-width: 600px;
                margin: 0 auto;
                padding: 0 24px;
            }
        }

        /* Mobile */
        @media (max-width: 767px) {
            .content-inner { padding: 0; }
        }

        /* Sidebar nav links */
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 14px;
            text-decoration: none; color: var(--text-muted);
            font-size: 14px; font-weight: 500;
            transition: all 0.2s; margin-bottom: 4px;
        }
        .sidebar-link:hover {
            background: rgba(245,158,11,0.08);
            color: var(--text-main);
        }
        .sidebar-link.active {
            background: rgba(245,158,11,0.12);
            color: var(--amber);
        }
        .sidebar-link .nav-icon { font-size: 20px; flex-shrink: 0; }

        /* Scan button sidebar */
        .sidebar-scan-btn {
            display: flex; align-items: center; gap: 12px;
            padding: 14px 16px; border-radius: 14px;
            background: var(--amber); color: #0F0F0F;
            text-decoration: none; font-size: 14px; font-weight: 600;
            transition: all 0.2s; margin-bottom: 4px;
        }
        .sidebar-scan-btn:hover {
            background: #FBBF24;
            box-shadow: 0 4px 20px rgba(245,158,11,0.3);
        }

        /* Space utility */
        .space-y-2 > * + * { margin-top: 8px; }
        .space-y-3 > * + * { margin-top: 12px; }
        .space-y-4 > * + * { margin-top: 16px; }
        .space-y-5 > * + * { margin-top: 20px; }
    </style>
</head>
<body>

{{-- Flash message --}}
@if(session('success'))
<div x-data="{ show: true }" x-show="show"
     x-init="setTimeout(() => show=false, 3000)"
     style="position:fixed; top:20px; left:50%; transform:translateX(-50%);
            background:#10B981; color:#fff; padding:12px 24px; border-radius:12px;
            font-size:14px; font-weight:500; z-index:9999;
            box-shadow:0 4px 20px rgba(0,0,0,0.3); white-space:nowrap;">
    {{ session('success') }}
</div>
@endif

<div class="app-wrapper">

    {{-- ===== SIDEBAR (Desktop) ===== --}}
    <aside class="sidebar">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}"
           style="display:flex; align-items:center; gap:12px;
                  text-decoration:none; margin-bottom:40px;">
            <div style="width:40px; height:40px; background:var(--amber); border-radius:12px;
                        display:flex; align-items:center; justify-content:center;
                        font-size:20px; flex-shrink:0;">
                🧾
            </div>
            <span class="font-display" style="font-size:22px; color:var(--text-main);">
                Prudent
            </span>
        </a>

        {{-- Nav Links --}}
        <nav style="flex:1;">

            <p style="font-size:11px; color:var(--text-muted); letter-spacing:0.1em;
                      text-transform:uppercase; margin-bottom:10px; padding:0 16px;">
                Menu
            </p>

            <a href="{{ route('dashboard') }}"
               class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">🏠</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('receipt.upload') }}"
               class="sidebar-scan-btn {{ request()->routeIs('receipt.*') ? 'active' : '' }}">
                <span class="nav-icon">📸</span>
                <span>Scan Struk</span>
            </a>

            <a href="{{ route('transaction.index') }}"
               class="sidebar-link {{ request()->routeIs('transaction.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span>
                <span>Riwayat</span>
            </a>

            <a href="{{ route('budget.index') }}"
               class="sidebar-link {{ request()->routeIs('budget.*') ? 'active' : '' }}">
                <span class="nav-icon">💰</span>
                <span>Budget</span>
            </a>

            <a href="{{ route('report.index') }}"
               class="sidebar-link {{ request()->routeIs('report.*') ? 'active' : '' }}">
                <span class="nav-icon">📊</span>
                <span>Laporan</span>
            </a>

            <div style="height:1px; background:var(--border); margin:16px 0;"></div>

            <a href="{{ route('profile.index') }}"
               class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="nav-icon">👤</span>
                <span>Profil</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link"
                        style="width:100%; border:none; cursor:pointer;
                               background:none; text-align:left;">
                    <span class="nav-icon">🚪</span>
                    <span>Logout</span>
                </button>
            </form>
        </nav>

        {{-- User info bottom --}}
        <div style="padding:16px; background:var(--bg-input); border-radius:16px;
                    border:1px solid var(--border); margin-top:auto;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:10px; flex-shrink:0;
                            background:var(--amber); display:flex; align-items:center;
                            justify-content:center; font-weight:700; color:#0F0F0F; font-size:16px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div style="min-width:0;">
                    <p style="font-size:13px; font-weight:600; white-space:nowrap;
                              overflow:hidden; text-overflow:ellipsis;">
                        {{ Auth::user()->name }}
                    </p>
                    <p style="font-size:11px; color:var(--text-muted); white-space:nowrap;
                              overflow:hidden; text-overflow:ellipsis;">
                        {{ Auth::user()->email }}
                    </p>
                </div>
            </div>
        </div>

    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="main-content">
        <div class="content-inner">
            @yield('content')
        </div>
    </main>

</div>

{{-- ===== BOTTOM NAV (Mobile) ===== --}}
<nav class="bottom-nav">
    <div style="display:flex; flex-direction:row; align-items:flex-end;
                justify-content:space-around; padding:0 8px; width:100%;">

        <a href="{{ route('dashboard') }}"
           style="display:flex; flex-direction:column; align-items:center;
                  gap:4px; text-decoration:none; flex:1; font-size:11px;
                  {{ request()->routeIs('dashboard') ? 'color:var(--amber);' : 'color:var(--text-muted);' }}">
            <span style="font-size:22px;">🏠</span>
            <span>Home</span>
        </a>

        <a href="{{ route('transaction.index') }}"
           style="display:flex; flex-direction:column; align-items:center;
                  gap:4px; text-decoration:none; flex:1; font-size:11px;
                  {{ request()->routeIs('transaction.*') ? 'color:var(--amber);' : 'color:var(--text-muted);' }}">
            <span style="font-size:22px;">📋</span>
            <span>Riwayat</span>
        </a>

        <div style="display:flex; flex-direction:column; align-items:center;
                    flex:1; gap:4px; padding-bottom:2px;">
            <a href="{{ route('receipt.upload') }}"
               style="width:56px; height:56px; background:var(--amber);
                      border-radius:18px; display:flex; align-items:center;
                      justify-content:center; font-size:24px; text-decoration:none;
                      box-shadow:0 4px 20px rgba(245,158,11,0.4); margin-top:-20px;">
                📸
            </a>
            <span style="font-size:11px; color:var(--text-muted);">Scan</span>
        </div>

        <a href="{{ route('report.index') }}"
           style="display:flex; flex-direction:column; align-items:center;
                  gap:4px; text-decoration:none; flex:1; font-size:11px;
                  {{ request()->routeIs('report.*') ? 'color:var(--amber);' : 'color:var(--text-muted);' }}">
            <span style="font-size:22px;">📊</span>
            <span>Laporan</span>
        </a>

        <a href="{{ route('profile.index') }}"
           style="display:flex; flex-direction:column; align-items:center;
                  gap:4px; text-decoration:none; flex:1; font-size:11px;
                  {{ request()->routeIs('profile.*') ? 'color:var(--amber);' : 'color:var(--text-muted);' }}">
            <span style="font-size:22px;">👤</span>
            <span>Profil</span>
        </a>

    </div>
</nav>

</body>
</html>