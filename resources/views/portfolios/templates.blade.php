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
                <input type="radio" name="template_id" value="{{ $template->id }}" @checked(old('template_id', $portfolio->template_id) == $template->id) required>
                <span class="template-preview preview-{{ $template->slug }}">
                    @if ($template->slug === 'simple')
                        <span class="preview-topline"></span><span class="preview-title">{{ $portfolio->full_name }}</span><span class="preview-rule"></span><span class="preview-columns"><i></i><i></i></span><span class="preview-lines"></span>
                    @elseif ($template->slug === 'modern')
                        <span class="preview-modern-head"><i></i><b>{{ $portfolio->full_name }}</b></span><span class="preview-modern-grid"><i></i><i></i><i></i></span><span class="preview-lines"></span>
                    @else
                        <span class="preview-creative-side"><i></i></span><span class="preview-creative-copy"><b>{{ $portfolio->full_name }}</b><i></i><i></i></span><span class="preview-creative-orb"></span>
                    @endif
                </span>
                <span class="template-meta"><span><strong>{{ $template->name }}</strong><small>{{ $template->description }}</small></span><span class="radio-mark" aria-hidden="true">✓</span></span>
                <span class="template-tag">{{ ['simple' => 'QUIET & CLEAR', 'modern' => 'STRUCTURED & BOLD', 'creative' => 'EXPRESSIVE & EDITORIAL'][$template->slug] }}</span>
            </label>
        @endforeach
    </div>
    @error('template_id')<p class="field-error template-error">{{ $message }}</p>@enderror
    <div class="form-actions template-actions"><a class="button button-quiet" href="{{ route('portfolios.index') }}">Save for later</a><button class="button button-primary" type="submit">Preview portfolio <span aria-hidden="true">→</span></button></div>
</form>
@endsection
