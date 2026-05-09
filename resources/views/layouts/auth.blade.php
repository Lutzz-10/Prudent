<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'Prudent')</title>
    {{-- Favicon --}}
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="apple-touch-icon" href="{{ asset('prudent-logo-192.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark:    #0F0F0F;
            --bg-card:    #1A1A1A;
            --bg-input:   #242424;
            --amber:      #F59E0B;
            --amber-dark: #B45309;
            --text-main:  #F5F0E8;
            --text-muted: #6B6B6B;
            --border:     #2A2A2A;
        }
        * { box-sizing: border-box; }
        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            font-family: 'DM Sans', sans-serif;
            min-height: 100dvh;
        }
        .font-display { font-family: 'DM Serif Display', serif; }

        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: 0 32px 64px rgba(0,0,0,0.5);
        }
        .input-field {
            background: var(--bg-input);
            border: 1px solid var(--border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 14px 16px;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-field:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        .input-field::placeholder { color: var(--text-muted); }
        .btn-primary {
            background: var(--amber);
            color: #0F0F0F;
            border: none;
            border-radius: 12px;
            padding: 15px;
            width: 100%;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            letter-spacing: 0.02em;
        }
        .btn-primary:hover {
            background: #FBBF24;
            box-shadow: 0 8px 24px rgba(245,158,11,0.3);
        }
        .btn-primary:active { transform: scale(0.98); }
        .label-field {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            display: block;
            margin-bottom: 8px;
        }
        .logo-mark {
            width: 48px;
            height: 48px;
            background: var(--amber);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.5s ease forwards; }
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }

        /* Desktop auth layout */
    @media (min-width: 768px) {
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100dvh;
            padding: 40px 20px;
        }

        /* Decorative left panel */
        .auth-split {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 580px;
            border-radius: 28px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        }

        .auth-left {
            display: flex !important;
            flex: 1;
            background: var(--bg-card);
            padding: 48px;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid var(--border);
        }

        .auth-right {
            width: 420px;
            flex-shrink: 0;
            background: var(--bg-dark);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
    }

    @media (max-width: 767px) {
        .auth-split {
            display: block;
            width: 100%;
        }
        .auth-left { display: none !important; }
        .auth-right { padding: 0; background: transparent; }
    }
    </style>
</head>
<body class="flex flex-col items-center justify-center px-5 py-10 relative z-10">

    {{-- Decorative background circle --}}
    <div style="
        position:fixed; top:-120px; right:-80px;
        width:320px; height:320px;
        background: radial-gradient(circle, rgba(245,158,11,0.12) 0%, transparent 70%);
        border-radius:50%; pointer-events:none; z-index:0;">
    </div>

    <div class="w-full max-w-sm relative z-10">
        @yield('content')
    </div>

</body>
</html>