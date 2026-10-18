@extends('layouts.app')

@section('title', 'Reset password')

@section('content')
<section class="auth-layout" aria-labelledby="auth-title">
    <div class="auth-story">
        <a class="auth-story-brand" href="{{ route('home') }}"><span class="brand-mark">S</span> StuDyIo</a>
        <div class="auth-story-copy">
            <p class="eyebrow">One more step</p>
            <h1>Choose a<br><em>new password.</em></h1>
            <p>Set a new password to sign back in and continue building your portfolio.</p>
        </div>
        <span class="auth-story-index">SECURE ACCOUNT RECOVERY</span>
        <span class="auth-story-shape" aria-hidden="true"></span>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-wrap">
            <p class="eyebrow">Reset password</p>
            <h2 id="auth-title">Create a new password</h2>
            <p class="auth-form-intro">Choose a strong password you haven’t used for this account before.</p>
            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" maxlength="255" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>New password</span><input type="password" name="password" autocomplete="new-password" required><small>Use at least 8 characters.</small>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>Confirm new password</span><input type="password" name="password_confirmation" autocomplete="new-password" required>@error('password_confirmation')<small class="field-error">{{ $message }}</small>@enderror</label>
                <button class="button button-primary auth-submit" type="submit">Save new password <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Back to log in</a></p>
        </div>
    </div>
</section>
@endsection
