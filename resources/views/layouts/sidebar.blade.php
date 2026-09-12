{{-- resources/views/layouts/sidebar.blade.php --}}
<!-- Mobile Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="toggleSidebar()"></div>

<!-- Left Sidebar Shell -->
<aside class="app-sidebar" id="appSidebar">
    <!-- Sidebar Header / Branding -->
    <div class="sidebar-header">
        <a href="{{ route('home') }}" class="sidebar-brand">
            <span class="brand-icon">📊</span>
            <div class="brand-info">
                <span class="brand-title">Smart-Results</span>
                <span class="brand-subtitle">School Portal</span>
            </div>
        </a>
        <button type="button" class="sidebar-close-btn" onclick="toggleSidebar()" aria-label="{{ __('Close') }}">
            ✕
        </button>
    </div>

    <!-- User Profile Card -->
    @auth
    <div class="sidebar-user-card">
        <div class="user-avatar">
            {{ strtoupper(substr(auth()->user()->username ?? 'U', 0, 1)) }}
        </div>
        <div class="user-details">
            <div class="user-name" title="{{ auth()->user()->username }}">{{ auth()->user()->username }}</div>
            <div class="user-role-badge">{{ auth()->user()->role }}</div>
        </div>
    </div>
    @endauth

    <!-- Sidebar Navigation Links -->
    <div class="sidebar-nav-container">
        <nav class="sidebar-nav">
            @auth
                <!-- SECTION: MAIN / DASHBOARDS -->
                <div class="nav-section-title">{{ __('MAIN') }}</div>

                <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">{{ __('Home') }}</span>
                </a>

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">⚙️</span>
                        <span class="nav-label">{{ __('Admin Panel') }}</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isLeader() || auth()->user()->role === 'Academic Master' || auth()->user()->isTeacher())
                    <a href="{{ route('leader.dashboard') }}" class="nav-item {{ request()->routeIs('leader.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span>
                        <span class="nav-label">{{ __('Academic Results') }}</span>
                    </a>
                @endif

                <!-- SECTION: ACADEMICS & ATTENDANCE -->
                <div class="nav-section-title">{{ __('ACADEMICS') }}</div>

                @if(auth()->user()->isAdmin() || auth()->user()->isTeacher() || auth()->user()->role === 'Academic Master' || auth()->user()->teacherAssignments()->exists())
                    <a href="{{ route('teacher.marks') }}" class="nav-item {{ request()->routeIs('teacher.marks*') ? 'active' : '' }}">
                        <span class="nav-icon">📝</span>
                        <span class="nav-label">{{ __('Marks Entry') }}</span>
                    </a>
                    <a href="{{ route('teacher.attendance') }}" class="nav-item {{ request()->routeIs('teacher.attendance*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span>
                        <span class="nav-label">{{ __('Student Attendance') }}</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isLeader() || auth()->user()->role === 'Academic Master')
                    <a href="{{ route('leader.attendance') }}" class="nav-item {{ request()->routeIs('leader.attendance*') ? 'active' : '' }}">
                        <span class="nav-icon">📑</span>
                        <span class="nav-label">{{ __('Attendance Reports') }}</span>
                    </a>
                @endif

                <a href="{{ route('timetable.index') }}" class="nav-item {{ request()->routeIs('timetable*') ? 'active' : '' }}">
                    <span class="nav-icon">📅</span>
                    <span class="nav-label">{{ __('Timetable') }}</span>
                </a>

                @if(auth()->user()->isParent() || auth()->user()->isAdmin())
                    <a href="{{ route('parent.reports') }}" class="nav-item {{ request()->routeIs('parent.reports*') ? 'active' : '' }}">
                        <span class="nav-icon">🎓</span>
                        <span class="nav-label">{{ __('Student Reports') }}</span>
                    </a>
                @endif

                @if(auth()->user()->isAccountant())
                    <a href="{{ route('accountant.fees') }}" class="nav-item {{ request()->routeIs('accountant.*') ? 'active' : '' }}">
                        <span class="nav-icon">💳</span>
                        <span class="nav-label">{{ __('Fee Desk') }}</span>
                    </a>
                @endif

                <!-- SECTION: ADMINISTRATION (Admin Only) -->
                @if(auth()->user()->isAdmin())
                    <div class="nav-section-title">{{ __('ADMINISTRATION') }}</div>

                    <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label">{{ __('Users') }}</span>
                    </a>

                    <a href="{{ route('admin.students') }}" class="nav-item {{ request()->routeIs('admin.students*') ? 'active' : '' }}">
                        <span class="nav-icon">🎓</span>
                        <span class="nav-label">{{ __('Students') }}</span>
                    </a>

                    <a href="{{ route('admin.dashboard') }}#sms-section" class="nav-item">
                        <span class="nav-icon">📱</span>
                        <span class="nav-label">{{ __('Send SMS') }}</span>
                    </a>

                    <a href="{{ route('admin.backup.export') }}" class="nav-item">
                        <span class="nav-icon">💾</span>
                        <span class="nav-label">{{ __('Export Backup Data') }}</span>
                    </a>

                    <a href="{{ route('admin.dashboard') }}#delete-marks-section" class="nav-item nav-item-danger">
                        <span class="nav-icon">🗑️</span>
                        <span class="nav-label">{{ __('Delete Results') }}</span>
                    </a>
                @endif

                <!-- SECTION: ACCOUNT & SYSTEM -->
                <div class="nav-section-title">{{ __('SYSTEM') }}</div>

                <a href="{{ route('password.change') }}" class="nav-item {{ request()->routeIs('password.change*') ? 'active' : '' }}">
                    <span class="nav-icon">🔒</span>
                    <span class="nav-label">{{ __('Change Password') }}</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm" style="display: block; margin-top: 4px;">
                    @csrf
                    <button type="submit" class="nav-item nav-item-logout" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left; font-family: inherit;">
                        <span class="nav-icon">🚪</span>
                        <span class="nav-label">{{ __('Logout') }}</span>
                    </button>
                </form>

            @else
                <!-- Guest Navigation Links -->
                <div class="nav-section-title">{{ __('PORTAL ENTRY') }}</div>

                <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">{{ __('Home') }}</span>
                </a>

                <a href="{{ route('home') }}#portals" class="nav-item">
                    <span class="nav-icon">🏢</span>
                    <span class="nav-label">{{ __('Portals') }}</span>
                </a>

                <a href="{{ route('home') }}#features" class="nav-item">
                    <span class="nav-icon">⚡</span>
                    <span class="nav-label">{{ __('Features') }}</span>
                </a>

                <a href="{{ route('home') }}#contact" class="nav-item">
                    <span class="nav-icon">📞</span>
                    <span class="nav-label">{{ __('Contact') }}</span>
                </a>

                <a href="{{ route('login') }}" class="nav-item {{ request()->routeIs('login') ? 'active' : '' }}" style="background: rgba(37, 99, 235, 0.2); color: #60a5fa; font-weight: 700; margin-top: 8px;">
                    <span class="nav-icon">🔑</span>
                    <span class="nav-label">{{ __('Login Entry') }}</span>
                </a>
            @endauth
        </nav>
    </div>

    <!-- Sidebar Footer / Language Switcher -->
    <div class="sidebar-footer">
        <div class="sidebar-lang-switch">
            <a href="{{ route('lang.switch', 'en') }}" class="sidebar-lang-btn {{ app()->getLocale() == 'en' ? 'active' : '' }}" title="English">
                <span>🇬🇧</span> EN
            </a>
            <a href="{{ route('lang.switch', 'sw') }}" class="sidebar-lang-btn {{ app()->getLocale() == 'sw' ? 'active' : '' }}" title="Kiswahili">
                <span>🇹🇿</span> SW
            </a>
        </div>
        <div class="sidebar-version">v2.5 &bull; Smart-Schools</div>
    </div>
</aside>

<style>
    /* Self-contained Sidebar Styles */
    :root {
        --sidebar-w: 260px;
        --sidebar-bg: #0f172a;
        --sidebar-header: #0b1120;
        --sidebar-border: #1e293b;
        --sidebar-text: #94a3b8;
        --sidebar-hover: #1e293b;
        --sidebar-active: #2563eb;
    }

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

    .app-sidebar {
        width: var(--sidebar-w);
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
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.15);
        text-align: left;
    }

    .sidebar-header {
        height: 62px;
        padding: 0 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--sidebar-header);
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
    }

    .sidebar-brand .brand-title {
        font-size: 16px;
        font-weight: 800;
        color: #38bdf8;
        letter-spacing: -0.3px;
        line-height: 1.2;
    }

    .sidebar-brand .brand-subtitle {
        font-size: 10.5px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
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
    }

    .sidebar-close-btn:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.1);
    }

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

    .sidebar-user-card .user-avatar {
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
    }

    .sidebar-user-card .user-details {
        min-width: 0;
        flex: 1;
    }

    .sidebar-user-card .user-name {
        font-size: 13.5px;
        font-weight: 700;
        color: #f8fafc;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-user-card .user-role-badge {
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
        background: var(--sidebar-hover);
        transform: translateX(2px);
    }

    .nav-item.active {
        background: var(--sidebar-active);
        color: #ffffff !important;
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

    .nav-item-danger:hover,
    .nav-item-logout:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171 !important;
    }

    .sidebar-footer {
        padding: 12px 16px;
        background: var(--sidebar-header);
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

    .sidebar-lang-btn:hover { color: #ffffff; }
    .sidebar-lang-btn.active { background: #2563eb; color: #ffffff; }

    .sidebar-version {
        font-size: 10px;
        color: #475569;
        font-weight: 600;
    }

    /* Desktop Collapsed state */
    body.sidebar-collapsed .app-sidebar {
        transform: translateX(-100%);
    }

    /* Mobile off-canvas drawer */
    @media (max-width: 1023px) {
        .app-sidebar {
            transform: translateX(-100%);
        }
        .sidebar-close-btn {
            display: block;
        }
        body.sidebar-open .app-sidebar {
            transform: translateX(0);
        }
        body.sidebar-open .sidebar-backdrop {
            display: block;
            opacity: 1;
        }
    }

    /* Print hiding */
    @media print {
        .app-sidebar,
        .sidebar-backdrop,
        .sidebar-toggle-btn,
        .sidebar-close-btn {
            display: none !important;
        }
    }
</style>

<script>
    function toggleSidebar() {
        if (window.innerWidth >= 1024) {
            document.body.classList.toggle('sidebar-collapsed');
            try {
                localStorage.setItem('sidebar_collapsed', document.body.classList.contains('sidebar-collapsed') ? 'true' : 'false');
            } catch(e) {}
        } else {
            document.body.classList.toggle('sidebar-open');
        }
    }

    (function() {
        try {
            if (window.innerWidth >= 1024 && localStorage.getItem('sidebar_collapsed') === 'true') {
                document.body.classList.add('sidebar-collapsed');
            }
        } catch(e) {}
    })();
</script>
