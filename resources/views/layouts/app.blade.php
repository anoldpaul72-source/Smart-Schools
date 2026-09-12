<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart-Results | School Management Portal')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #1e3a8a;
            --accent: #3b82f6;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;

            /* Left Sidebar Theme Tokens */
            --sidebar-width: 260px;
            --sidebar-bg: #0f172a;
            --sidebar-header-bg: #0b1120;
            --sidebar-border: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-hover-bg: #1e293b;
            --sidebar-active-bg: #2563eb;
            --sidebar-active-text: #ffffff;
            --topbar-height: 62px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
        }

        /* ================= App Shell & Sidebar Layout ================= */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(3px);
            z-index: 1040;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        /* Sidebar Container */
        .app-sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            border-right: 1px solid var(--sidebar-border);
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s ease;
            box-shadow: 4px 0 15px rgba(0, 0, 0, 0.12);
        }

        /* Sidebar Header */
        .sidebar-header {
            height: var(--topbar-height);
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--sidebar-header-bg);
            border-bottom: 1px solid var(--sidebar-border);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .sidebar-brand .brand-icon {
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-brand .brand-title {
            font-size: 16px;
            font-weight: 800;
            color: #38bdf8;
            letter-spacing: -0.3px;
            display: block;
            line-height: 1.2;
        }

        .sidebar-brand .brand-subtitle {
            font-size: 10.5px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: block;
        }

        .sidebar-close-btn {
            display: none;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 18px;
            cursor: pointer;
            padding: 6px 10px;
            border-radius: 6px;
            transition: all 0.15s ease;
        }

        .sidebar-close-btn:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* User Profile Card in Sidebar */
        .sidebar-user-card {
            padding: 12px 16px;
            margin: 12px 14px 6px 14px;
            background: #1e293b;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: white;
            font-weight: 800;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
        }

        .user-details {
            min-width: 0;
            flex: 1;
        }

        .user-name {
            font-size: 13.5px;
            font-weight: 700;
            color: #f8fafc;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-badge {
            display: inline-block;
            font-size: 10px;
            font-weight: 800;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.14);
            border: 1px solid rgba(56, 189, 248, 0.28);
            padding: 1px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        /* Sidebar Navigation Area */
        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            padding: 10px 12px;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            padding: 12px 10px 4px 10px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 8px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .nav-item:hover {
            color: #f8fafc;
            background: var(--sidebar-hover-bg);
            transform: translateX(2px);
        }

        .nav-item.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text) !important;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        }

        .nav-item .nav-icon {
            font-size: 16px;
            width: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-item-danger:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171 !important;
        }

        .nav-item-logout:hover {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171 !important;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            padding: 12px 16px;
            background: var(--sidebar-header-bg);
            border-top: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-lang-switch {
            display: flex;
            align-items: center;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 2px;
            gap: 2px;
        }

        .sidebar-lang-btn {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-decoration: none;
            padding: 3px 8px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }

        .sidebar-lang-btn:hover {
            color: #ffffff;
        }

        .sidebar-lang-btn.active {
            background: #2563eb;
            color: #ffffff;
        }

        .sidebar-version {
            font-size: 10px;
            color: #475569;
            font-weight: 600;
        }

        /* ================= Main Layout Wrapper ================= */
        .app-main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - var(--sidebar-width));
            background-color: var(--bg);
            transition: margin-left 0.28s cubic-bezier(0.4, 0, 0.2, 1), width 0.28s ease;
        }

        /* App Topbar */
        .app-topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sidebar-toggle-btn {
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #334155;
            font-size: 18px;
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .sidebar-toggle-btn:hover {
            background: #e2e8f0;
            color: var(--primary);
        }

        .topbar-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Desktop Collapse State */
        body.sidebar-collapsed .app-sidebar {
            transform: translateX(-100%);
        }

        body.sidebar-collapsed .app-main-wrapper {
            margin-left: 0;
            width: 100%;
        }

        /* Mobile & Tablet Responsive Breakdown */
        @media (max-width: 1023px) {
            .app-sidebar {
                transform: translateX(-100%);
            }

            .sidebar-close-btn {
                display: block;
            }

            .app-main-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            body.sidebar-open .app-sidebar {
                transform: translateX(0);
            }

            body.sidebar-open .sidebar-backdrop {
                display: block;
                opacity: 1;
            }
        }

        /* Buttons and Elements */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13.5px;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff !important;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            border-color: var(--border);
            color: var(--text-main);
            background: #ffffff;
        }

        .btn-outline:hover {
            border-color: var(--text-muted);
            background: #f1f5f9;
        }

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c !important;
            border-color: #fecaca;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .main-content {
            flex: 1;
            max-width: 1320px;
            width: 100%;
            margin: 0 auto;
            padding: 24px;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* User Profile Pill */
        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f1f5f9;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 12.5px;
            font-weight: 600;
        }

        .user-pill .role-tag {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 10.5px;
            text-transform: uppercase;
        }

        /* Language Switcher in Topbar */
        .lang-switcher {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px;
            gap: 3px;
        }

        .lang-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            color: var(--text-muted) !important;
            text-decoration: none;
            border-radius: 16px;
            transition: all 0.2s ease;
            line-height: 1;
        }

        .lang-btn:hover {
            color: var(--primary) !important;
            background: rgba(255, 255, 255, 0.7) !important;
        }

        .lang-btn.active {
            background: #ffffff !important;
            color: var(--primary) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 24px 20px;
            text-align: center;
            font-size: 13px;
            margin-top: auto;
        }

        footer b {
            color: #ffffff;
        }

        /* Responsive Table */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        /* Pagination & Fix for Unstyled Laravel SVGs */
        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
            vertical-align: middle !important;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 15px 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #ffffff;
            color: var(--text-main);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .page-item.active .page-link {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            color: var(--text-muted);
            background: #f1f5f9;
            cursor: not-allowed;
            border-color: var(--border);
        }

        .page-item .page-link:hover:not(.disabled) {
            background: #eff6ff;
            border-color: var(--primary);
            color: var(--primary);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* Form elements */
        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #334155;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            background-color: #ffffff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Print Media Query */
        @media print {
            .app-sidebar,
            .sidebar-backdrop,
            .app-topbar,
            .sidebar-toggle-btn,
            .sidebar-close-btn,
            footer {
                display: none !important;
            }

            .app-main-wrapper {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .main-content {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Universal Left Sidebar -->
    @include('layouts.sidebar')

    <!-- Main Content App Wrapper -->
    <div class="app-main-wrapper" id="appMainWrapper">
        @unless(View::hasSection('no_global_header'))
        <header class="app-topbar">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle-btn" onclick="toggleSidebar()" title="{{ __('Toggle Sidebar') }}" aria-label="{{ __('Toggle Sidebar') }}">
                    ☰
                </button>
                <div class="topbar-title">
                    <span>📊</span>
                    <span>@yield('title', 'Smart-Results')</span>
                </div>
            </div>

            <div class="topbar-right">
                @auth
                    <div class="user-pill">
                        <span>👤 {{ auth()->user()->username }}</span>
                        <span class="role-tag">{{ auth()->user()->role }}</span>
                    </div>

                    <a href="{{ route('password.change') }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;" title="{{ __('Change Password') }}">
                        <span>🔒</span> <span style="display: none; @media(min-width: 640px){ display: inline; }">{{ __('Change Password') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 6px 14px; font-size: 12px;">{{ __('Login Entry') }}</a>
                @endauth

                <!-- Language Switcher in Topbar -->
                <div class="lang-switcher" title="Choose Language / Chagua Lugha">
                    <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        <span>🇬🇧</span> EN
                    </a>
                    <a href="{{ route('lang.switch', 'sw') }}" class="lang-btn {{ app()->getLocale() == 'sw' ? 'active' : '' }}">
                        <span>🇹🇿</span> SW
                    </a>
                </div>
            </div>
        </header>
        @endunless

        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success">
                    <span>✔️</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <span>❌</span>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <span>⚠️</span>
                    <div>
                        <ul style="margin-left: 20px;">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

        <footer>
            <div style="max-width: 900px; margin: 0 auto 16px auto; display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 22px; font-size: 13px; color: #cbd5e1;">
                <span>📍 Mbezi Louis, Dar es Salaam</span>
                <span>📞 <a href="tel:+255621530804" style="color: #93c5fd; text-decoration: none; font-weight: 600;">+255 621 530 804</a> / <a href="tel:+255657276380" style="color: #93c5fd; text-decoration: none; font-weight: 600;">+255 657 276 380</a></span>
                <span>✉️ <a href="mailto:sylvesterarnold72@gmail.com" style="color: #93c5fd; text-decoration: none; font-weight: 600;">sylvesterarnold72@gmail.com</a></span>
            </div>
            <p>&copy; 2026 <b>Smart-Results Engine</b>. {{ __('All Institutional Rights Reserved.') }}</p>
            <p style="font-size: 11px; margin-top: 5px; opacity: 0.6;">{{ __('Powered by Secure MVC Architecture & CSRF Shield.') }}</p>
        </footer>
    </div>

    @yield('scripts')
</body>
</html>
