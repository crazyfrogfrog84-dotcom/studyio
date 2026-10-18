@extends('layouts.app')

@section('title', 'Log in')

@section('content')
<section class="auth-layout" aria-labelledby="auth-title">
    <div class="auth-story">
        <a class="auth-story-brand" href="{{ route('home') }}"><span class="brand-mark">S</span> StuDyIo</a>
        <div class="auth-story-copy">
            <p class="eyebrow">Your work, in one place</p>
            <h1>Pick up where<br><em>you left off.</em></h1>
            <p>Sign in to refine your portfolio, explore its presentation, and keep your story current.</p>
        </div>
        <span class="auth-story-index">A PORTFOLIO BUILDER FOR WHAT’S NEXT</span>
        <span class="auth-story-shape" aria-hidden="true"></span>
    </div>
    <div class="auth-form-side">
        <div class="auth-form-wrap">
            <p class="eyebrow">Welcome back</p>
            <h2 id="auth-title">Log in to StuDyIo</h2>
            <p class="auth-form-intro">Use the email and password connected to your account.</p>
            <form class="auth-form" method="POST" action="{{ route('login.store') }}">
                @csrf
                <label class="field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="field"><span>Password</span><input type="password" name="password" autocomplete="current-password" required>@error('password')<small class="field-error">{{ $message }}</small>@enderror</label>
                <label class="auth-remember"><input type="checkbox" name="remember" value="1"> Keep me signed in</label>
                <button class="button button-primary auth-submit" type="submit">Log in <span aria-hidden="true">↗</span></button>
            </form>
            <p class="auth-switch">New to StuDyIo? <a href="{{ route('register') }}">Create an account</a></p>
        </div>
    </div>
</section>
@endsection
