@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
<section class="auth-layout" aria-labelledby="auth-title">
    <div class="auth-story">
        <a class="auth-story-brand" href="{{ route('home') }}" aria-label="StuDyIo home"><span class="brand-mark" aria-hidden="true">S</span></a>
        <div class="auth-story-copy">
            <p class="eyebrow">A fresh start</p>
            <h1>Let’s get you<br><em>back in.</em></h1>
            <p>Request a secure link to choose a new password for your StuDyIo account.</p>
        </div>
        <span class="auth-story-index">YOUR PORTFOLIO IS WAITING</span>
        <span class="auth-story-shape" aria-hidden="true"></span>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-wrap">
            <p class="eyebrow">Account recovery</p>
            <h2 id="auth-title">Forgot your password?</h2>
            <p class="auth-form-intro">Enter the email address for your account. If it matches, we’ll send a reset link.</p>
            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required autofocus>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
                <button class="button button-primary auth-submit" type="submit">Send reset link</button>
            </form>
            <p class="auth-switch"><a href="{{ route('login') }}">Back to log in</a></p>
        </div>
    </div>
</section>
@endsection
