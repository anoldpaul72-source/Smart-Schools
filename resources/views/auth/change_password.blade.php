@extends('layouts.app')

@section('title', __('Change Password') . ' | Smart-Results')

@section('styles')
<style>
    .change-pw-container {
        max-width: 520px;
        margin: 40px auto;
        padding: 0 16px;
    }

    .change-pw-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 36px 32px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .card-header-box {
        text-align: center;
        margin-bottom: 28px;
    }

    .pw-icon-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #eff6ff;
        color: var(--primary);
        font-size: 26px;
        margin-bottom: 14px;
        border: 1px solid #bfdbfe;
    }

    .card-title {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        letter-spacing: -0.3px;
    }

    .card-subtitle {
        font-size: 13.5px;
        color: #64748b;
    }

    .user-info-banner {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 16px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .user-info-left {
        font-size: 13px;
        color: #475569;
        font-weight: 600;
    }

    .user-info-left strong {
        color: #0f172a;
    }

    .user-role-tag {
        background: #e0f2fe;
        color: #0369a1;
        font-size: 11px;
        font-weight: 800;
        padding: 3px 9px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .pw-input-wrap {
        position: relative;
    }

    .pw-input-wrap input {
        padding-right: 42px;
    }

    .pw-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        font-size: 16px;
        padding: 4px;
        transition: color 0.15s;
    }

    .pw-toggle-btn:hover {
        color: #334155;
    }

    .pw-hint {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    .btn-submit-pw {
        width: 100%;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 10px;
        background-color: var(--primary);
        color: #ffffff !important;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.25);
    }

    .btn-submit-pw:hover {
        background-color: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
    }

    .btn-back-portal {
        width: 100%;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 10px;
        background: #f8fafc;
        color: #475569 !important;
        border: 1px solid #cbd5e1;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
        transition: all 0.2s;
    }

    .btn-back-portal:hover {
        background: #f1f5f9;
        color: #0f172a !important;
        border-color: #94a3b8;
    }
</style>
@endsection

@section('content')
@php
    $user = auth()->user();
    $backRoute = match($user->role) {
        'Admin'                       => route('admin.dashboard'),
        'Teacher'                     => route('teacher.marks'),
        'Parent'                      => route('parent.reports'),
        'Accountant'                  => route('accountant.fees'),
        'Headmaster', 'Academic Master' => route('leader.dashboard'),
        default                       => route('home'),
    };
@endphp

<div class="change-pw-container">
    <div class="change-pw-card">
        <div class="card-header-box">
            <div class="pw-icon-badge">🔒</div>
            <h1 class="card-title">{{ __('Change Account Password') }}</h1>
            <p class="card-subtitle">{{ __('Update your login credentials securely') }}</p>
        </div>

        <div class="user-info-banner">
            <div class="user-info-left">
                👤 <strong>{{ $user->name ?: $user->username }}</strong> ({{ $user->username }})
            </div>
            <span class="user-role-tag">{{ $user->role }}</span>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">{{ __('Current Password') }}</label>
                <div class="pw-input-wrap">
                    <input type="password" name="current_password" id="current_password" placeholder="{{ __('Enter your current password') }}" required autofocus>
                    <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('current_password', this)" title="Show / Hide">
                        👁️
                    </button>
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="new_password">{{ __('New Password (minimum 6 characters)') }}</label>
                <div class="pw-input-wrap">
                    <input type="password" name="new_password" id="new_password" minlength="6" placeholder="{{ __('Enter new password (min 6 chars)') }}" required>
                    <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('new_password', this)" title="Show / Hide">
                        👁️
                    </button>
                </div>
                <div class="pw-hint">{{ __('New Password (minimum 6 characters)') }}</div>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="new_password_confirmation">{{ __('Confirm New Password') }}</label>
                <div class="pw-input-wrap">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" minlength="6" placeholder="{{ __('Repeat new password') }}" required>
                    <button type="button" class="pw-toggle-btn" onclick="togglePasswordVisibility('new_password_confirmation', this)" title="Show / Hide">
                        👁️
                    </button>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <button type="submit" class="btn-submit-pw">
                    <span>💾</span>
                    <span>{{ __('Update Password') }}</span>
                </button>

                <a href="{{ $backRoute }}" class="btn-back-portal">
                    <span>⬅️</span>
                    <span>{{ __('Back to Portal') }}</span>
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            btn.style.opacity = '1';
        } else {
            input.type = 'password';
            btn.style.opacity = '0.6';
        }
    }
</script>
@endsection
