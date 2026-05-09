<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'Prudent')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Favicon --}}
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('prudent-logo-192.png') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
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
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            background: var(--bg-dark);
            color: var(--text-main);
            font-family: 'DM Sans', sans-serif;
            min-height: 100dvh;
            max-width: 480px;
            margin: 0 auto;
        }
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
        .btn-primary {
            background: var(--amber); color: #0F0F0F; border: none;
            border-radius: 12px; padding: 15px; width: 100%;
            font-family: 'DM Sans', sans-serif; font-weight: 600; font-size: 15px;
            cursor: pointer; transition: background 0.2s, transform 0.1s;
        }
        .btn-primary:hover { background: #FBBF24; }
        .btn-primary:active { transform: scale(0.98); }
        .label-field {
            font-size: 12px; font-weight: 500; color: var(--text-muted);
            letter-spacing: 0.08em; text-transform: uppercase; display: block; margin-bottom: 8px;
        }

        /* Bottom nav */
        .bottom-nav {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 480px;
            background: rgba(26,26,26,0.95);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            padding: 12px 0 20px;
            z-index: 100;
        }
        .bottom-nav a {
            display: flex; flex-direction: column; align-items: center;
            gap: 4px; text-decoration: none; flex: 1;
            font-size: 10px; color: var(--text-muted);
            transition: color 0.2s;
        }
        .bottom-nav a.active, .bottom-nav a:hover { color: var(--amber); }
        .bottom-nav .nav-icon { font-size: 22px; }

        /* Scan button center */
        .scan-btn-wrap {
            position: relative; flex: 1; display: flex;
            flex-direction: column; align-items: center;
        }
        .scan-btn {
            width: 56px; height: 56px; background: var(--amber);
            border-radius: 18px; display: flex; align-items: center;
            justify-content: center; font-size: 24px;
            box-shadow: 0 4px 20px rgba(245,158,11,0.4);
            margin-top: -20px; transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }
        .scan-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(245,158,11,0.5); }

        /* Smooth scroll */
    html { scroll-behavior: smooth; }

    /* Scrollbar hidden tapi tetap scrollable */
    body::-webkit-scrollbar { display: none; }
    body { -ms-overflow-style: none; scrollbar-width: none; }

    /* Tap highlight mobile */
    * { -webkit-tap-highlight-color: transparent; }

    /* Safe area iPhone X ke atas */
    .bottom-nav {
        padding-bottom: max(20px, env(safe-area-inset-bottom));
    }

    /* Transisi halaman --  */
    main {
        animation: pageFadeIn 0.25s ease forwards;
    }
    @keyframes pageFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Active state tombol --  */
    .btn-primary:active,
    button:active { opacity: 0.85; }

    /* Input number — sembunyikan arrow --  */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; }
    input[type=number] { -moz-appearance: textfield; }

    /* Select styling --  */
    select.input-field {
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B6B6B' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    /* Date input styling --  */
    input[type=date]::-webkit-calendar-picker-indicator {
        filter: invert(0.4);
        cursor: pointer;
    }

    /* Space for bottom nav */
    .pb-32 { padding-bottom: 120px !important; }
    </style>
</head>
<body>

    {{-- Flash message --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show=false, 3000)"
         style="position:fixed; top:16px; left:50%; transform:translateX(-50%);
                background:#10B981; color:#fff; padding:12px 20px; border-radius:12px;
                font-size:14px; font-weight:500; z-index:999;
                box-shadow: 0 4px 20px rgba(0,0,0,0.3); white-space:nowrap;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Main content --}}
    <main>
        @yield('content')
    </main>

    {{-- Bottom Navigation --}}
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

        {{-- Scan button tengah --}}
        <div style="display:flex; flex-direction:column; align-items:center;
                    flex:1; gap:4px; padding-bottom:2px;">
            <a href="{{ route('receipt.upload') }}"
               style="width:56px; height:56px; background:var(--amber);
                      border-radius:18px; display:flex; align-items:center;
                      justify-content:center; font-size:24px; text-decoration:none;
                      box-shadow:0 4px 20px rgba(245,158,11,0.4);
                      margin-top:-20px; transition:transform 0.2s, box-shadow 0.2s;"
               onmouseenter="this.style.transform='translateY(-2px)'"
               onmouseleave="this.style.transform='translateY(0)'">
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