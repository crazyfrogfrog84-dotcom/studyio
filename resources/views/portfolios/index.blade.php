@extends('layouts.app')

@section('title', 'My portfolios')

@section('content')
<section class="page-heading">
    <div><p class="eyebrow">Your work, together</p><h1>My portfolios</h1><p>Open a preview, make an edit, or start something new.</p></div>
    <a class="button button-primary" href="{{ route('portfolios.create') }}">Create portfolio <span aria-hidden="true">↗</span></a>
</section>

@if ($portfolios->isEmpty())
    <section class="empty-state"><span class="empty-icon">✳</span><h2>Your first portfolio starts here</h2><p>Add your story and choose a template when you are ready.</p><a class="button button-primary" href="{{ route('portfolios.create') }}">Create portfolio</a></section>
@else
    <div class="portfolio-grid">
        @foreach ($portfolios as $portfolio)
            <article class="manage-card">
                <div class="manage-card-top">
                    <div class="avatar avatar-small">{{ mb_substr($portfolio->full_name, 0, 1) }}</div>
                    <span class="template-pill">{{ $portfolio->template?->name ?? 'Choose a template' }}</span>
                </div>
                <h2>{{ $portfolio->full_name }}</h2><p>{{ $portfolio->email }}</p>
                <div class="manage-card-actions">
                    @if ($portfolio->template_id)<a class="button button-small button-dark" href="{{ route('portfolios.show', $portfolio) }}">View</a>@else<a class="button button-small button-dark" href="{{ route('portfolios.templates.edit', $portfolio) }}">Choose template</a>@endif
                    <a class="button button-small button-quiet" href="{{ route('portfolios.edit', $portfolio) }}">Edit</a>
                    <a class="button button-small button-danger" href="{{ route('portfolios.confirm-delete', $portfolio) }}">Delete</a>
                </div>
            </article>
        @endforeach
    </div>
    <div class="pagination-wrap">{{ $portfolios->links() }}</div>
@endif
@endsection
