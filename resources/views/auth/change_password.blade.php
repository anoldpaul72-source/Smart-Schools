@extends('layouts.app')

@section('title', 'Change Password | Smart-Results')

@section('content')
<div style="max-width: 500px; margin: 40px auto;">
    <div style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h2 style="margin-bottom: 20px; font-size: 20px;">🔒 Change Account Password</h2>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password (minimum 6 characters)</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
