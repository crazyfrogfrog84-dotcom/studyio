@extends('layouts.app')

@section('title', $portfolio->full_name.' portfolio')

@section('content')
<div class="preview-toolbar">
    <a class="back-link" href="{{ route('portfolios.index') }}">← Manage portfolios</a>
    <div>
        <a class="button button-small button-quiet" href="{{ route('portfolios.templates.edit', $portfolio) }}">Change template</a>
        <a class="button button-small button-dark" href="{{ route('portfolios.edit', $portfolio) }}">Edit portfolio</a>
        <a class="button button-small button-danger" href="{{ route('portfolios.confirm-delete', $portfolio) }}">Delete</a>
    </div>
</div>

@if (!$portfolio->template)
    <section class="empty-state">
        <span class="empty-icon" aria-hidden="true">✳</span>
        <h2>Choose a template to see your portfolio</h2>
        <p>Your information is saved, and you can change it any time.</p>
        <a class="button button-primary" href="{{ route('portfolios.templates.edit', $portfolio) }}">Choose template</a>
    </section>
@else
    <article class="portfolio-preview template-{{ $portfolio->template->slug }}">
        <header class="portfolio-cover">
            <div class="preview-kicker"><span>Portfolio</span><span>{{ now()->year }}</span></div>
            <div class="preview-intro">
                @if ($pictureUrl)
                    <img class="profile-photo" src="{{ $pictureUrl }}" alt="{{ $portfolio->full_name }}">
                @else
                    <div class="profile-photo avatar" aria-hidden="true">{{ mb_substr($portfolio->full_name, 0, 1) }}</div>
                @endif
                <div>
                    <p class="eyebrow">Hello, I'm</p>
                    <h1>{{ $portfolio->full_name }}</h1>
                    <p class="preview-email">{{ $portfolio->email }}@if ($portfolio->contact_number) · {{ $portfolio->contact_number }}@endif</p>
                </div>
            </div>
            @if ($portfolio->about_me)
                <p class="preview-about">{{ $portfolio->about_me }}</p>
            @endif
            @if ($portfolio->socialLinks->isNotEmpty())
                <div class="preview-links">
                    @foreach ($portfolio->socialLinks as $link)
                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->label ?: $link->platform ?: 'Link' }} <span aria-hidden="true">↗</span></a>
                    @endforeach
                </div>
            @endif
        </header>

        <div class="preview-content">
            @if ($portfolio->projects->isNotEmpty())
                <section class="preview-section projects-section">
                    <div class="preview-section-heading">
                        @include('portfolios.partials.section-marker', ['number' => '01', 'icon' => 'projects', 'templateSlug' => $portfolio->template->slug])
                        <h2>Selected projects</h2>
                    </div>
                    <div class="preview-project-grid">
                        @foreach ($portfolio->projects as $project)
                            <article class="preview-project-card">
                                @if (!empty($projectImageUrls[$project->screenshot_path]))
                                    <img class="project-image" src="{{ $projectImageUrls[$project->screenshot_path] ?? '' }}" alt="{{ $project->title }} project screenshot">
                                @else
                                    <div class="project-placeholder">
                                        <span>Project / {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        <svg aria-hidden="true" viewBox="0 0 48 48" fill="none"><path d="M24 3v42M3 24h42M9.2 9.2l29.6 29.6m0-29.6L9.2 38.8" stroke="currentColor" stroke-width="2" /></svg>
                                    </div>
                                @endif
                                <div class="project-copy">
                                    <h3>{{ $project->title }}</h3>
                                    @if ($project->description)<p>{{ $project->description }}</p>@endif
                                    @if ($project->project_url)<a class="text-link" href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">View project <span aria-hidden="true">↗</span></a>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="preview-two-column">
                @if ($portfolio->education->isNotEmpty())
                    <section class="preview-section">
                        <div class="preview-section-heading">
                            @include('portfolios.partials.section-marker', ['number' => '02', 'icon' => 'education', 'templateSlug' => $portfolio->template->slug])
                            <h2>Education</h2>
                        </div>
                        <div class="timeline-list">
                            @foreach ($portfolio->education as $item)
                                <article class="timeline-item">
                                    <span class="timeline-dot" aria-hidden="true"></span>
                                    <h3>{{ $item->qualification ?: $item->field_of_study ?: $item->institution }}</h3>
                                    <p class="muted">{{ $item->institution }}@if ($item->institution && $item->field_of_study) · @endif{{ $item->field_of_study }}</p>
                                    @if ($item->description)<p>{{ $item->description }}</p>@endif
                                    <small>{{ $item->start_date?->format('Y') }}@if ($item->end_date) — {{ $item->end_date->format('Y') }}@endif</small>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
                @if ($portfolio->skills->isNotEmpty())
                    <section class="preview-section">
                        <div class="preview-section-heading">
                            @include('portfolios.partials.section-marker', ['number' => '03', 'icon' => 'skills', 'templateSlug' => $portfolio->template->slug])
                            <h2>Skills</h2>
                        </div>
                        <div class="skill-list">
                            @foreach ($portfolio->skills as $skill)
                                <span class="skill-chip">{{ $skill->name }}@if ($skill->proficiency)<small>{{ $skill->proficiency }}</small>@endif</span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            @if ($portfolio->workExperiences->isNotEmpty())
                <details class="preview-section preview-details">
                    <summary>
                        <div class="preview-section-heading">
                            @include('portfolios.partials.section-marker', ['number' => '04', 'icon' => 'work', 'templateSlug' => $portfolio->template->slug])
                            <h2>Experience</h2>
                        </div>
                        <span class="summary-caret" aria-hidden="true">＋</span>
                    </summary>
                    <div class="timeline-list">
                        @foreach ($portfolio->workExperiences as $job)
                            <article class="timeline-item">
                                <span class="timeline-dot" aria-hidden="true"></span>
                                <h3>{{ $job->role ?: $job->company }}</h3>
                                <p class="muted">{{ $job->company }}</p>
                                @if ($job->description)<p>{{ $job->description }}</p>@endif
                                <small>{{ $job->start_date?->format('M Y') }}@if ($job->end_date) — {{ $job->end_date->format('M Y') }}@endif</small>
                            </article>
                        @endforeach
                    </div>
                </details>
            @endif
            @if ($portfolio->address || $portfolio->contact_number)
                <details class="preview-section preview-details">
                    <summary>
                        <div class="preview-section-heading">
                            @include('portfolios.partials.section-marker', ['number' => '05', 'icon' => 'contact', 'templateSlug' => $portfolio->template->slug])
                            <h2>Get in touch</h2>
                        </div>
                        <span class="summary-caret" aria-hidden="true">＋</span>
                    </summary>
                    <div class="contact-details">
                        @if ($portfolio->contact_number)<p><strong>Phone</strong> {{ $portfolio->contact_number }}</p>@endif
                        @if ($portfolio->address)<p><strong>Based in</strong> {{ $portfolio->address }}</p>@endif
                    </div>
                </details>
            @endif
        </div>
        <footer class="preview-footer"><span>{{ $portfolio->full_name }}</span><span>Made with StuDyIo</span></footer>
    </article>
@endif
@endsection
