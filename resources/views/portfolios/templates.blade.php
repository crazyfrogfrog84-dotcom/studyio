@extends('layouts.app')

@section('title', 'Choose a template')

@section('content')
<section class="form-heading template-heading">
    <a class="back-link" href="{{ route('portfolios.edit', $portfolio) }}">← Edit your information</a>
    <p class="eyebrow">One last touch</p>
    <h1>Choose your layout</h1>
    <p>Pick the style that feels like you. You can change it later.</p>
</section>
<form method="POST" action="{{ route('portfolios.templates.update', $portfolio) }}">
    @csrf @method('PUT')
    <div class="template-grid">
        @foreach ($templates as $template)
            <label class="template-card {{ $template->slug }}-card">
                <input type="radio" name="template_id" value="{{ $template->id }}" aria-label="Choose {{ $template->name }}" @checked(old('template_id', $portfolio->template_id) == $template->id) required>
                <span class="template-preview preview-{{ $template->slug }}" aria-hidden="true">
                    @if ($template->slug === 'simple')
                        <span class="mini-simple-top">PORTFOLIO <b>2026</b></span><strong class="mini-simple-name">{{ $portfolio->full_name }}</strong><span class="mini-simple-accent"></span><span class="mini-simple-line"></span><span class="mini-simple-row"><b>SELECTED WORK</b><i></i><i></i></span><span class="mini-simple-row"><b>EXPERIENCE</b><i></i><i></i></span>
                    @elseif ($template->slug === 'modern')
                        <span class="mini-modern-sidebar"><i></i><b>{{ $portfolio->full_name }}</b><em></em><em></em><em></em><em></em></span><span class="mini-modern-main"><i class="mini-modern-hero"></i><span class="mini-modern-bento"><i></i><i></i><i></i><i></i></span></span>
                    @else
                        <span class="mini-creative-copy"><b>{{ $portfolio->full_name }}</b><i></i><i></i><em>01</em></span><span class="mini-creative-photo"></span><span class="mini-creative-orbit"></span>
                    @endif
                </span>
                <span class="template-meta"><span><strong>{{ $template->name }}</strong><small>{{ $template->description }}</small></span><span class="radio-mark" aria-hidden="true">✓</span></span>
                <span class="template-tag">{{ ['simple' => 'EDITORIAL · QUIET & CLEAR', 'modern' => 'PRODUCT · STRUCTURED', 'creative' => 'EXPRESSIVE · ASYMMETRIC'][$template->slug] }}</span>
            </label>
        @endforeach
    </div>
    @error('template_id')<p class="field-error template-error">{{ $message }}</p>@enderror
    <div class="form-actions template-actions"><a class="button button-quiet" href="{{ route('portfolios.index') }}">Save for later</a><button class="button button-primary" type="submit">Preview portfolio <span aria-hidden="true">→</span></button></div>
</form>
@endsection
