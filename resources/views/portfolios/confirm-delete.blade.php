@extends('layouts.app')

@section('title', 'Confirm portfolio deletion')

@section('content')
<section class="confirm-page">
    <a class="back-link" href="{{ route('portfolios.show', $portfolio) }}">Keep portfolio</a>
    <p class="eyebrow">Please confirm</p>
    <h1>Delete {{ $portfolio->full_name }}’s portfolio?</h1>
    <p>This also removes its education, skills, projects, work experience and social links.</p>
    <div class="confirm-actions">
        <a class="button button-quiet" href="{{ route('portfolios.show', $portfolio) }}">Keep portfolio</a>
        <form method="POST" action="{{ route('portfolios.destroy', $portfolio) }}">@csrf @method('DELETE')<button class="button button-danger" type="submit">Delete portfolio</button></form>
    </div>
</section>
@endsection
