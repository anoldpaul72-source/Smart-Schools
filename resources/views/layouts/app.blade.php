<!DOCTYPE html>
<html lang="en">
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
            flex-direction: column;
        }

        header {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.5px;
        }

        .brand-logo span.badge-lar {
            background: #ffe4e6;
            color: #e11d48;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            letter-spacing: 0;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 600;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-links a:hover {
            color: var(--primary);
            background: #eff6ff;
        }

        .lang-switcher {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3px;
            gap: 3px;
            margin-left: 8px;
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

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
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
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 30px 24px;
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
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }

        .user-pill .role-tag {
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 30px 20px;
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
    </style>
    @yield('styles')
</head>
<body>

    @unless(View::hasSection('no_global_header'))
    <header>
        <div class="header-container">
            <a href="{{ route('home') }}" class="brand-logo">
                <span>📊 Smart-Results</span>
                <span class="badge-lar">Laravel 12</span>
            </a>

            <nav class="nav-links">
                <a href="{{ route('home') }}">{{ __('Home') }}</a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">{{ __('Admin Panel') }}</a>
                        <a href="{{ route('admin.users') }}">{{ __('Users') }}</a>
                        <a href="{{ route('admin.students') }}">{{ __('Students') }}</a>
                    @elseif(auth()->user()->isTeacher())
                        <a href="{{ route('teacher.marks') }}">{{ __('Marks Entry') }}</a>
                        <a href="{{ route('teacher.attendance') }}">{{ __('Attendance') }}</a>
                        <a href="{{ route('timetable.index') }}">📅 {{ __('Timetable') }}</a>
                    @elseif(auth()->user()->isParent())
                        <a href="{{ route('parent.reports') }}">{{ __('Student Reports') }}</a>
                    @elseif(auth()->user()->isLeader())
                        <a href="{{ route('leader.dashboard') }}">{{ __('Analytics Dashboard') }}</a>
                        <a href="{{ route('timetable.index') }}">📅 {{ __('School Timetable') }}</a>
                    @elseif(auth()->user()->isAccountant())
                        <a href="{{ route('accountant.fees') }}">{{ __('Fee Desk') }}</a>
                    @endif

                    <div class="user-pill">
                        <span>👤 {{ auth()->user()->username }}</span>
                        <span class="role-tag">{{ auth()->user()->role }}</span>
                    </div>

                    <a href="{{ route('password.change') }}" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; text-decoration: none;" title="{{ __('Change Password') }}">
                        <span>🔒</span> <span>{{ __('Change Password') }}</span>
                    </a>

                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="padding: 6px 12px; font-size: 12px;">{{ __('Logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('home') }}#portals">{{ __('Portals') }}</a>
                    <a href="{{ route('home') }}#features">{{ __('Features') }}</a>
                    <a href="{{ route('login') }}" class="btn btn-primary">{{ __('Login Entry') }}</a>
                @endauth

                <!-- Language Switcher -->
                <div class="lang-switcher" title="Choose Language / Chagua Lugha">
                    <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}">
                        <span>🇬🇧</span> EN
                    </a>
                    <a href="{{ route('lang.switch', 'sw') }}" class="lang-btn {{ app()->getLocale() == 'sw' ? 'active' : '' }}">
                        <span>🇹🇿</span> SW
                    </a>
                </div>
            </nav>
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
        <p>&copy; 2026 <b>Smart-Results Engine</b>. {{ __('All Institutional Rights Reserved.') }}</p>
        <p style="font-size: 11px; margin-top: 5px; opacity: 0.6;">{{ __('Powered by Laravel 12 MVC Architecture & Secure CSRF Shield.') }}</p>
    </footer>

    @yield('scripts')
</body>
</html>
