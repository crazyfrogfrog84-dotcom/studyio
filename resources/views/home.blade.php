@extends('layouts.app')

@section('title', 'Home')

@section('content')
<section class="home-hero">
    <div class="hero-copy">
        <p class="eyebrow">A portfolio, made personal</p>
        <h1>Make your next<br><em>first impression</em>.</h1>
        <p class="hero-intro">Bring your education, projects and experience together in one portfolio you can share with confidence.</p>
        <div class="hero-actions">
            <a class="button button-primary" href="{{ route('portfolios.create') }}">Create portfolio</a>
            <a class="button button-secondary button-home-login" href="{{ route('login') }}">Log in</a>
        </div>
        <p class="hero-note"><span class="status-dot"></span> Your work stays with your account · Edit any time</p>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
        <div class="art-card art-back"><span>01 / PROJECT</span><div class="art-lines"><i></i><i></i><i></i></div><div class="art-shape"></div></div>
        <div class="art-card art-front"><div class="art-avatar">S</div><span class="art-label">YOUR STORY</span><strong>Ideas into<br>impact.</strong><div class="art-tag">PORTFOLIO / 2026</div></div>
        <span class="art-spark spark-one"></span><span class="art-spark spark-two"></span>
    </div>
</section>

<section class="home-steps">
    <div><span class="step-number">01</span><strong>Share your story</strong><p>Add the details that matter to you.</p></div>
    <div><span class="step-number">02</span><strong>Choose a look</strong><p>Pick one of three distinct layouts.</p></div>
    <div><span class="step-number">03</span><strong>Preview and refine</strong><p>Update or remove it whenever you like.</p></div>
</section>
@endsection
