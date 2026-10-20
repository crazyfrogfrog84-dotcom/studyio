@extends('layouts.app')

@section('title', $portfolio->full_name.' portfolio')

@section('content')
@php
    $templateSlug = $portfolio->template?->slug ?? 'simple';
    $skillIcons = [
        'php' => 'php/php-original', 'laravel' => 'laravel/laravel-original',
        'javascript' => 'javascript/javascript-original', 'js' => 'javascript/javascript-original',
        'typescript' => 'typescript/typescript-original', 'python' => 'python/python-original',
        'html' => 'html5/html5-original', 'html5' => 'html5/html5-original',
        'css' => 'css3/css3-original', 'css3' => 'css3/css3-original',
        'react' => 'react/react-original', 'vue' => 'vuejs/vuejs-original',
        'vue.js' => 'vuejs/vuejs-original', 'figma' => 'figma/figma-original',
        'git' => 'git/git-original', 'postgresql' => 'postgresql/postgresql-original',
        'mysql' => 'mysql/mysql-original', 'tailwind css' => 'tailwindcss/tailwindcss-original',
        'canva' => 'canva/canva-original', 'node.js' => 'nodejs/nodejs-original',
    ];
@endphp

<div class="preview-toolbar">
    <a class="back-link" href="{{ route('portfolios.index') }}">Manage portfolios</a>
    <div>
        <a class="button button-small button-quiet" href="{{ route('portfolios.templates.edit', $portfolio) }}">Change template</a>
        <a class="button button-small button-dark" href="{{ route('portfolios.edit', $portfolio) }}">Edit portfolio</a>
        <a class="button button-small button-danger" href="{{ route('portfolios.confirm-delete', $portfolio) }}">Delete</a>
    </div>
</div>

@if (!$portfolio->template)
    <section class="empty-state">
        <h2>Choose a template to see your portfolio</h2>
        <p>Your information is saved, and you can change it any time.</p>
        <a class="button button-primary" href="{{ route('portfolios.templates.edit', $portfolio) }}">Choose template</a>
    </section>
@elseif ($templateSlug === 'simple')
    <article class="portfolio-preview template-simple simple-editorial">
        <header class="simple-masthead">
            <div class="simple-topline"><span>Portfolio</span><span>{{ now()->year }}</span></div>
            <div class="simple-name-line"><div><p class="simple-label">Selected work &amp; experience</p><h1>{{ $portfolio->full_name }}</h1></div>@if ($pictureUrl)<img class="simple-portrait" src="{{ $pictureUrl }}" alt="Portrait of {{ $portfolio->full_name }}">@endif</div>
            <div class="simple-contact-line"><a href="mailto:{{ $portfolio->email }}">{{ $portfolio->email }}</a>@if ($portfolio->contact_number)<span>{{ $portfolio->contact_number }}</span>@endif @if ($portfolio->address)<span>{{ $portfolio->address }}</span>@endif</div>
            @if ($portfolio->about_me)<p class="simple-intro">{{ $portfolio->about_me }}</p>@endif
            @if ($portfolio->socialLinks->isNotEmpty())<nav class="simple-social" aria-label="Social and website links">@foreach ($portfolio->socialLinks as $link)<a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->label ?: $link->platform ?: 'Website' }}</a>@endforeach</nav>@endif
        </header>
        <div class="simple-content">
            @if ($portfolio->projects->isNotEmpty())<section class="simple-section" id="projects"><div class="simple-section-title"><span>01</span><h2>Selected work</h2></div>@foreach ($portfolio->projects as $project)<article class="simple-project-row">
                @if (!empty($projectImageUrls[$project->screenshot_path]))<img src="{{ $projectImageUrls[$project->screenshot_path] }}" alt="{{ $project->title }} project screenshot">@endif
                <div><p class="simple-label">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }} / PROJECT</p><h3>{{ $project->title }}</h3>@if ($project->tags)<ul class="simple-tags">@foreach ($project->tags as $tag)<li>{{ $tag }}</li>@endforeach</ul>@endif @if ($project->description)<p>{{ $project->description }}</p>@endif @if ($project->project_url)<a class="simple-link" href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">View project</a>@endif</div>
            </article>@endforeach</section>@endif
            @if ($portfolio->workExperiences->isNotEmpty())<section class="simple-section" id="experience"><div class="simple-section-title"><span>02</span><h2>Experience</h2></div>@foreach ($portfolio->workExperiences as $job)<article class="simple-entry-row"><time>{{ $job->start_date?->format('Y') }} — {{ $job->end_date?->format('Y') ?? 'Present' }}</time><div><h3>{{ $job->role ?: $job->company }}</h3><p class="simple-entry-subtitle">{{ $job->company }}</p>@if ($job->description)<p>{{ $job->description }}</p>@endif</div></article>@endforeach</section>@endif
            @if ($portfolio->education->isNotEmpty())<section class="simple-section" id="education"><div class="simple-section-title"><span>03</span><h2>Education</h2></div>@foreach ($portfolio->education as $item)<article class="simple-entry-row"><time>{{ $item->start_date?->format('Y') }}@if ($item->end_date) — {{ $item->end_date->format('Y') }}@endif</time><div><h3>{{ $item->qualification ?: $item->field_of_study ?: $item->institution }}</h3><p class="simple-entry-subtitle">{{ $item->institution }}@if ($item->field_of_study) · {{ $item->field_of_study }}@endif</p>@if ($item->description)<p>{{ $item->description }}</p>@endif</div></article>@endforeach</section>@endif
            @if ($portfolio->skills->isNotEmpty())<section class="simple-section" id="skills"><div class="simple-section-title"><span>04</span><h2>Skills</h2></div><ul class="simple-skills">@foreach ($portfolio->skills as $skill)<li>{{ $skill->name }}</li>@endforeach</ul></section>@endif
            @if ($portfolio->address || $portfolio->contact_number || $portfolio->socialLinks->isNotEmpty())<section class="simple-section simple-contact" id="contact"><div class="simple-section-title"><span>05</span><h2>Contact</h2></div><p><a href="mailto:{{ $portfolio->email }}">{{ $portfolio->email }}</a>@if ($portfolio->contact_number) · {{ $portfolio->contact_number }}@endif @if ($portfolio->address) · {{ $portfolio->address }}@endif</p></section>@endif
        </div>
        <footer class="simple-footer"><span>{{ $portfolio->full_name }}</span><span>Made with StuDyIo</span></footer>
    </article>
@elseif ($templateSlug === 'modern')
    <article class="portfolio-preview template-modern modern-layout">
        <aside class="modern-sidebar"><div class="modern-profile">
            @if ($pictureUrl)<img src="{{ $pictureUrl }}" alt="Portrait of {{ $portfolio->full_name }}">@else<div class="modern-avatar" aria-hidden="true">{{ mb_substr($portfolio->full_name, 0, 1) }}</div>@endif
            <h1>{{ $portfolio->full_name }}</h1><p>Portfolio / {{ now()->year }}</p>
        </div>
        <nav class="modern-nav" aria-label="Portfolio sections">
            @if ($portfolio->about_me)<a href="#about"><span>01</span> About</a>@endif
            @if ($portfolio->projects->isNotEmpty())<a href="#projects"><span>02</span> Projects</a>@endif
            @if ($portfolio->skills->isNotEmpty())<a href="#skills"><span>03</span> Skills</a>@endif
            @if ($portfolio->workExperiences->isNotEmpty())<a href="#experience"><span>04</span> Experience</a>@endif
            @if ($portfolio->education->isNotEmpty())<a href="#education"><span>05</span> Education</a>@endif
            @if ($portfolio->contact_number || $portfolio->address || $portfolio->socialLinks->isNotEmpty())<a href="#contact"><span>06</span> Contact</a>@endif
        </nav>
        <div class="modern-sidebar-contact"><p>{{ $portfolio->email }}</p>@if ($portfolio->contact_number)<p>{{ $portfolio->contact_number }}</p>@endif @if ($portfolio->address)<p>{{ $portfolio->address }}</p>@endif</div>
        @if ($portfolio->socialLinks->isNotEmpty())<div class="modern-sidebar-links">@foreach ($portfolio->socialLinks as $link)<a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->label ?: $link->platform ?: 'Website' }}</a>@endforeach</div>@endif</aside>
        <div class="modern-main">
            <header class="modern-hero">@if ($pictureUrl)<img class="modern-hero-image" src="{{ $pictureUrl }}" alt="">@endif<div class="modern-hero-copy"><p>PORTFOLIO <span>·</span> {{ now()->year }}</p><h2>{{ $portfolio->full_name }}</h2><span>Selected projects, skills and experience</span></div><span class="modern-hero-mark" aria-hidden="true">S.</span></header>
            @if ($portfolio->about_me)<section class="modern-about" id="about"><p class="modern-label">Overview</p><h2>{{ $portfolio->about_me }}</h2></section>@endif
            @if ($portfolio->projects->isNotEmpty())<section class="modern-section" id="projects"><div class="modern-section-head"><div><p class="modern-label">Work samples</p><h2>Selected projects</h2></div><span>{{ str_pad((string) $portfolio->projects->count(), 2, '0', STR_PAD_LEFT) }} projects</span></div><div class="modern-project-grid">
                @foreach ($portfolio->projects as $project)<article class="modern-project-card">@if (!empty($projectImageUrls[$project->screenshot_path]))<img class="modern-project-image" src="{{ $projectImageUrls[$project->screenshot_path] }}" alt="{{ $project->title }} project screenshot">@else<div class="modern-project-image modern-project-empty" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>@endif<div class="modern-project-copy"><p class="modern-label">PROJECT {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p><h3>{{ $project->title }}</h3>@if ($project->description)<p>{{ $project->description }}</p>@endif @if ($project->tags)<ul class="modern-tags">@foreach ($project->tags as $tag)<li>{{ $tag }}</li>@endforeach</ul>@endif @if ($project->project_url)<a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">Open project</a>@endif</div></article>@endforeach
            </div></section>@endif
            @if ($portfolio->skills->isNotEmpty())<section class="modern-section" id="skills"><div class="modern-section-head"><div><p class="modern-label">Toolkit</p><h2>Skills &amp; tools</h2></div></div><div class="modern-skill-grid">
                @foreach ($portfolio->skills as $skill)@php($skillKey = strtolower(trim($skill->name)))<div class="modern-skill">@if (isset($skillIcons[$skillKey]))<span class="modern-skill-icon" aria-hidden="true"><img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/{{ $skillIcons[$skillKey] }}.svg" alt="" loading="lazy"></span>@else<span class="modern-skill-letter" aria-hidden="true">{{ mb_strtoupper(mb_substr($skill->name, 0, 1)) }}</span>@endif<span>{{ $skill->name }}</span>@if ($skill->proficiency)<small>{{ $skill->proficiency }}</small>@endif</div>@endforeach
            </div></section>@endif
            @if ($portfolio->workExperiences->isNotEmpty())<section class="modern-section" id="experience"><div class="modern-section-head"><div><p class="modern-label">Career</p><h2>Experience</h2></div></div><div class="modern-timeline">@foreach ($portfolio->workExperiences as $job)<article><span class="modern-date">{{ $job->start_date?->format('M Y') }} — {{ $job->end_date?->format('M Y') ?? 'Present' }}</span><div><h3>{{ $job->role ?: $job->company }}</h3><p class="modern-company">{{ $job->company }}</p>@if ($job->description)<p>{{ $job->description }}</p>@endif</div></article>@endforeach</div></section>@endif
            @if ($portfolio->education->isNotEmpty())<section class="modern-section" id="education"><div class="modern-section-head"><div><p class="modern-label">Learning</p><h2>Education</h2></div></div><div class="modern-timeline">@foreach ($portfolio->education as $item)<article><span class="modern-date">{{ $item->start_date?->format('Y') }}@if ($item->end_date) — {{ $item->end_date->format('Y') }}@endif</span><div><h3>{{ $item->qualification ?: $item->field_of_study ?: $item->institution }}</h3><p class="modern-company">{{ $item->institution }}@if ($item->field_of_study) · {{ $item->field_of_study }}@endif</p>@if ($item->description)<p>{{ $item->description }}</p>@endif</div></article>@endforeach</div></section>@endif
            @if ($portfolio->contact_number || $portfolio->address || $portfolio->socialLinks->isNotEmpty())<section class="modern-contact" id="contact"><div><p class="modern-label">Get in touch</p><h2>Let’s connect.</h2></div><div><a href="mailto:{{ $portfolio->email }}">{{ $portfolio->email }}</a>@if ($portfolio->contact_number)<p>{{ $portfolio->contact_number }}</p>@endif @if ($portfolio->address)<p>{{ $portfolio->address }}</p>@endif @foreach ($portfolio->socialLinks as $link)<a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->label ?: $link->platform ?: 'Website' }}</a>@endforeach</div></section>@endif
            <footer class="modern-footer"><span>{{ $portfolio->full_name }}</span><span>Made with StuDyIo</span></footer>
        </div>
    </article>
@else
    <article class="portfolio-preview template-creative creative-layout">
        <header class="creative-hero"><div class="creative-hero-copy"><p class="creative-kicker">Portfolio / {{ now()->year }} <span>Selected work</span></p><h1>{{ $portfolio->full_name }}</h1>@if ($portfolio->about_me)<p class="creative-intro">{{ $portfolio->about_me }}</p>@endif<div class="creative-hero-contact"><a href="mailto:{{ $portfolio->email }}">{{ $portfolio->email }}</a>@if ($portfolio->contact_number)<span>{{ $portfolio->contact_number }}</span>@endif</div></div>
            @if ($portfolio->profile_picture_path || $portfolio->skills->isNotEmpty())<div class="creative-portrait-stage">@if ($portfolio->profile_picture_path && $pictureUrl)<img class="creative-portrait" src="{{ $pictureUrl }}" alt="Portrait of {{ $portfolio->full_name }}">@else<div class="creative-portrait creative-monogram" aria-hidden="true">{{ mb_substr($portfolio->full_name, 0, 1) }}</div>@endif @if ($portfolio->skills->isNotEmpty())<div class="creative-orbit" aria-label="Skills orbit"><span class="creative-orbit-center">TOOLKIT</span><ul>@foreach ($portfolio->skills as $skill)<li style="--orbit-index: {{ $loop->index }}; --orbit-count: {{ $portfolio->skills->count() }}">{{ $skill->name }}</li>@endforeach</ul></div>@endif</div>@endif
        </header>
        @if ($portfolio->skills->isNotEmpty())<div class="creative-marquee" aria-label="Skills"><div class="creative-marquee-track">@foreach ($portfolio->skills as $skill)<span>{{ $skill->name }}</span><i aria-hidden="true"></i>@endforeach @foreach ($portfolio->skills as $skill)<span aria-hidden="true">{{ $skill->name }}</span><i aria-hidden="true"></i>@endforeach</div></div>@endif
        <div class="creative-content">
            @if ($portfolio->projects->isNotEmpty())<section class="creative-section" id="projects"><div class="creative-section-heading"><span class="creative-number">01</span><div><p>Ideas made tangible</p><h2>Selected work</h2></div></div><div class="creative-project-grid">
                @foreach ($portfolio->projects as $project)<article class="creative-project-card">@if (!empty($projectImageUrls[$project->screenshot_path]))<img class="creative-project-image" src="{{ $projectImageUrls[$project->screenshot_path] }}" alt="{{ $project->title }} project screenshot">@else<div class="creative-project-image creative-project-empty" aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>@endif<div class="creative-project-copy"><p class="creative-label">PROJECT {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p><h3>{{ $project->title }}</h3>@if ($project->description)<p>{{ $project->description }}</p>@endif @if ($project->tags)<ul class="creative-tags">@foreach ($project->tags as $tag)<li>{{ $tag }}</li>@endforeach</ul>@endif @if ($project->project_url)<a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">Visit project</a>@endif</div></article>@endforeach
            </div></section>@endif
            @if ($portfolio->workExperiences->isNotEmpty())<section class="creative-section" id="experience"><div class="creative-section-heading"><span class="creative-number">02</span><div><p>Where I’ve made a difference</p><h2>Experience</h2></div></div><div class="creative-entry-list">@foreach ($portfolio->workExperiences as $job)<article><time>{{ $job->start_date?->format('Y') }} — {{ $job->end_date?->format('Y') ?? 'Present' }}</time><div><h3>{{ $job->role ?: $job->company }}</h3><p class="creative-entry-subtitle">{{ $job->company }}</p>@if ($job->description)<p>{{ $job->description }}</p>@endif</div></article>@endforeach</div></section>@endif
            @if ($portfolio->education->isNotEmpty())<section class="creative-section" id="education"><div class="creative-section-heading"><span class="creative-number">03</span><div><p>Curiosity in practice</p><h2>Education</h2></div></div><div class="creative-entry-list">@foreach ($portfolio->education as $item)<article><time>{{ $item->start_date?->format('Y') }}@if ($item->end_date) — {{ $item->end_date->format('Y') }}@endif</time><div><h3>{{ $item->qualification ?: $item->field_of_study ?: $item->institution }}</h3><p class="creative-entry-subtitle">{{ $item->institution }}@if ($item->field_of_study) · {{ $item->field_of_study }}@endif</p>@if ($item->description)<p>{{ $item->description }}</p>@endif</div></article>@endforeach</div></section>@endif
            @if ($portfolio->address || $portfolio->contact_number || $portfolio->socialLinks->isNotEmpty())<section class="creative-section creative-contact" id="contact"><div class="creative-section-heading"><span class="creative-number">04</span><div><p>Start a conversation</p><h2>Get in touch</h2></div></div><div class="creative-contact-links"><a href="mailto:{{ $portfolio->email }}">{{ $portfolio->email }}</a>@if ($portfolio->contact_number)<span>{{ $portfolio->contact_number }}</span>@endif @if ($portfolio->address)<span>{{ $portfolio->address }}</span>@endif @foreach ($portfolio->socialLinks as $link)<a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer">{{ $link->label ?: $link->platform ?: 'Website' }}</a>@endforeach</div></section>@endif
        </div>
        <footer class="creative-footer"><span>{{ $portfolio->full_name }}</span><span>Made with StuDyIo</span></footer>
    </article>
@endif
@endsection
