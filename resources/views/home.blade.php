@extends('layouts.app')

@section('title', 'Smart-Results | Multi-School Performance Analytics Platform')

@section('styles')
<style>
    .hero {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
        color: white;
        padding: 80px 20px;
        text-align: center;
        margin: -30px -24px 0 -24px;
        border-radius: 0 0 24px 24px;
    }

    .hero-container {
        max-width: 860px;
        margin: 0 auto;
    }

    .hero h1 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 20px;
        letter-spacing: -1px;
        line-height: 1.2;
    }

    .hero p {
        font-size: 18px;
        opacity: 0.92;
        margin-bottom: 35px;
        font-weight: 400;
        max-width: 780px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-btn {
        background: #ffffff;
        color: var(--primary) !important;
        padding: 14px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .hero-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    /* Portals Grid */
    .portals {
        max-width: 1260px;
        margin: -50px auto 70px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 24px;
    }

    .portal-card {
        background: #ffffff;
        padding: 35px 25px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        text-align: center;
        transition: transform 0.3s, box-shadow 0.3s;
        border-top: 5px solid transparent;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .portal-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.1);
    }

    .card-admin { border-top-color: #ef4444; }
    .card-teacher { border-top-color: #10b981; }
    .card-parent { border-top-color: #f59e0b; }
    .card-accountant { border-top-color: #8b5cf6; }

    .portal-icon {
        font-size: 44px;
        margin-bottom: 20px;
    }

    .portal-card h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-main);
    }

    .portal-card p {
        color: var(--text-muted);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .btn-card {
        display: inline-block;
        padding: 10px 22px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        color: var(--text-main);
        font-weight: 700;
        font-size: 14px;
        transition: all 0.2s;
    }

    .portal-card:hover .btn-card {
        background-color: var(--primary);
        color: #ffffff;
        border-color: var(--primary);
    }

    /* Features Section */
    .features {
        max-width: 1080px;
        margin: 0 auto 80px auto;
        text-align: center;
    }

    .features h2 {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 40px;
        color: var(--text-main);
        letter-spacing: -0.5px;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .feature-item {
        background: #ffffff;
        padding: 30px 24px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .feature-item .f-icon {
        font-size: 36px;
        margin-bottom: 14px;
    }

    .feature-item h4 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .feature-item p {
        color: var(--text-muted);
        font-size: 14px;
    }
</style>
@endsection

@section('content')
    <section class="hero">
        <div class="hero-container">
            <h1>{{ __('Multi-School Performance Analytics Platform') }}</h1>
            <p>{{ __('A cloud-integrated school architecture designed to streamline student registration, monitor evaluation scores, and deliver instant academic report tracking directly to parents.') }}</p>
            
            @if(!empty($dashboardRoute))
                <a href="{{ $dashboardRoute }}" class="hero-btn">
                    <span>🔄</span> {{ __($dashboardText) }}
                </a>
            @else
                <a href="{{ route('login') }}" class="hero-btn">
                    <span>🚀</span> {{ __('Access System Portal') }}
                </a>
            @endif
        </div>
    </section>

    <section class="portals" id="portals">
        <!-- Admin Card -->
        <div class="portal-card card-admin">
            <div>
                <div class="portal-icon">⚙️</div>
                <h3>{{ __('Administration Portal') }}</h3>
                <p>{{ __('Manage multiple schools, ingest bulk CSV student lists, map systems, and manage registration records safely.') }}</p>
            </div>
            <a href="{{ route('login') }}" class="btn-card">{{ __('Enter Admin Panel') }}</a>
        </div>

        <!-- Teacher Card -->
        <div class="portal-card card-teacher">
            <div>
                <div class="portal-icon">👨‍🏫</div>
                <h3>{{ __('Academic Portal') }}</h3>
                <p>{{ __('Input term grades, compute test evaluation benchmarks, modify subject points, and export dynamic result metrics.') }}</p>
            </div>
            <a href="{{ route('login') }}" class="btn-card">{{ __('Enter Teacher Desk') }}</a>
        </div>

        <!-- Parent Card -->
        <div class="portal-card card-parent">
            <div>
                <div class="portal-icon">👪</div>
                <h3>{{ __('Parent Portal') }}</h3>
                <p>{{ __('Track your student\'s live terminal growth data, view direct report sheets, and configure profile parameters.') }}</p>
            </div>
            <a href="{{ route('login') }}" class="btn-card">{{ __('View Student Report') }}</a>
        </div>

        <!-- Accountant Card -->
        <div class="portal-card card-accountant">
            <div>
                <div class="portal-icon">💰</div>
                <h3>{{ __('Accountant Portal') }}</h3>
                <p>{{ __('Manage fee structures, record student fee payments, generate instant receipts, and track school financial balances.') }}</p>
            </div>
            <a href="{{ route('login') }}" class="btn-card">{{ __('Enter Accountant Desk') }}</a>
        </div>
    </section>

    <section class="features" id="features">
        <h2>{{ __('Engineered for Modern Institutions') }}</h2>
        <div class="features-grid">
            <div class="feature-item">
                <div class="f-icon">⏱️</div>
                <h4>{{ __('Zero-Latency Parsing') }}</h4>
                <p>{{ __('Process entire classroom data directories via streamlined CSV ingestion models instantly without timing out.') }}</p>
            </div>

            <div class="feature-item">
                <div class="f-icon">🔒</div>
                <h4>{{ __('Secure Cryptography & CSRF') }}</h4>
                <p>{{ __('Equipped with industry-standard Laravel Bcrypt protocols protecting user records and managing individual account privacy.') }}</p>
            </div>

            <div class="feature-item">
                <div class="f-icon">📱</div>
                <h4>{{ __('Fluid Architecture') }}</h4>
                <p>{{ __('Engineered responsively to fluidly adapt down to handheld mobile devices for seamless parent access anywhere.') }}</p>
            </div>
        </div>
    </section>
@endsection
