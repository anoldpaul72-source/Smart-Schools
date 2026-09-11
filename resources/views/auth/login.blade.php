@extends('layouts.app')

@section('title', 'System Login | Smart-Results')

@section('styles')
<style>
    .login-wrapper {
        min-height: calc(80vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 15px;
    }

    .login-card {
        background: #ffffff;
        border: 1px solid var(--border);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        width: 100%;
        max-width: 440px;
        padding: 40px 35px;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: var(--secondary);
        margin-bottom: 8px;
    }

    .login-header p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .password-field {
        position: relative;
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <h2>{{ __('System Login') }}</h2>
            <p>{{ __('Enter your institutional credentials to access your portal') }}</p>
        </div>

        <form method="POST" action="{{ route('login.post', [], false) }}">
            @csrf

            <div class="form-group">
                <label for="username">{{ __('Username') }}</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" placeholder="{{ __('Enter your username') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">{{ __('Password') }}</label>
                <div class="password-field">
                    <input type="password" name="password" id="password" placeholder="{{ __('Enter your password') }}" required>
                    <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Show/Hide Password">👁️</button>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: normal; font-size: 13px; cursor: pointer; margin-bottom: 0;">
                    <input type="checkbox" name="remember"> {{ __('Keep me signed in') }}
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px;">
                {{ __('Sign In') }}
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePasswordBtn');

    toggleBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.textContent = type === 'password' ? '👁️' : '🙈';
    });
</script>
@endsection
