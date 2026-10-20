@extends('layouts.app')

@section('title', 'Create account')

@section('content')
<section class="auth-layout" aria-labelledby="auth-title">
    <div class="auth-story auth-story-register">
        <a class="auth-story-brand" href="{{ route('home') }}" aria-label="StuDyIo home"><span class="brand-mark" aria-hidden="true">S</span></a>
        <div class="auth-story-copy">
            <p class="eyebrow">Start with your story</p>
            <h1>Your next chapter<br><em>starts here.</em></h1>
            <p>Bring your studies, projects, skills and experience into a portfolio that feels like you.</p>
        </div>
        <div class="auth-story-note"><span>01</span><p>One account keeps your portfolios private and ready to edit.</p></div>
        <span class="auth-story-index">DESIGNED TO HELP YOUR WORK SPEAK</span>
        <span class="auth-story-shape" aria-hidden="true"></span>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-wrap">
            <p class="eyebrow">A good place to begin</p>
            <h2 id="auth-title">Create your account</h2>
            <p class="auth-form-intro">Your details stay private. You control what goes into each portfolio.</p>
            <form class="auth-form" method="POST" action="{{ route('register.store') }}">
                @csrf
                <label class="field"><span>Full name</span><input name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" required autofocus>@error('name')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" required>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>Password</span><input type="password" name="password" autocomplete="new-password" required><small>Use at least 8 characters.</small>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>Confirm password</span><input type="password" name="password_confirmation" autocomplete="new-password" required>@error('password_confirmation')<small class="field-error">{{ $message }}</small>@enderror</label>
                <button class="button button-primary auth-submit" type="submit">Create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
        </div>
    </div>
</section>
@endsection
