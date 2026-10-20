@extends('layouts.app')

@section('title', $portfolio->exists ? 'Edit portfolio' : 'Create portfolio')

@section('content')
@php
    $educationRows = old('education', $portfolio->exists ? $portfolio->education->map(fn ($item) => $item->toArray())->all() : [[]]);
    $skillRows = old('skills', $portfolio->exists ? $portfolio->skills->map(fn ($item) => $item->toArray())->all() : [[]]);
    $projectRows = old('projects', $portfolio->exists ? $portfolio->projects->map(fn ($item) => $item->toArray())->all() : [[]]);
    $workRows = old('work_experience', $portfolio->exists ? $portfolio->workExperiences->map(fn ($item) => $item->toArray())->all() : [[]]);
    $socialRows = old('social_links', $portfolio->exists ? $portfolio->socialLinks->map(fn ($item) => $item->toArray())->all() : [[]]);
@endphp
<section class="form-heading">
    <a class="back-link" href="{{ $portfolio->exists ? route('portfolios.show', $portfolio) : route('home') }}">Back</a>
    <p class="eyebrow">{{ $portfolio->exists ? 'Update your details' : 'Start with the essentials' }}</p>
    <h1>{{ $portfolio->exists ? 'Edit your portfolio' : 'Tell your story' }}</h1>
    <p>Add what you have today. You can come back and refine it later.</p>
</section>

<form class="portfolio-form" method="POST" enctype="multipart/form-data" action="{{ $portfolio->exists ? route('portfolios.update', $portfolio) : route('portfolios.store') }}">
    @csrf
    @if ($portfolio->exists) @method('PUT') @endif

    <section class="form-section">
        <div class="section-heading"><span class="section-index">01</span><div><h2>About you</h2><p>Start with how people can find you.</p></div></div>
        <div class="form-grid">
            <label class="field"><span>Full name <b>*</b></span><input name="full_name" value="{{ old('full_name', $portfolio->full_name) }}" autocomplete="name" required maxlength="160" placeholder="e.g. Sam Rivera">@error('full_name')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Email <b>*</b></span><input type="email" name="email" value="{{ old('email', $portfolio->email) }}" autocomplete="email" required maxlength="255" placeholder="sam@example.com">@error('email')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Contact number <b>*</b></span><input name="contact_number" value="{{ old('contact_number', $portfolio->contact_number) }}" autocomplete="tel" inputmode="tel" pattern="[+]?[0-9]+" maxlength="60" placeholder="+15551234567" required><small>Use digits and an optional + at the beginning.</small>@error('contact_number')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field"><span>Address <b>*</b></span><input name="address" value="{{ old('address', $portfolio->address) }}" autocomplete="street-address" maxlength="1000" placeholder="City, country" required>@error('address')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field field-wide"><span>Profile picture <b>*</b></span><input type="file" name="profile_picture" accept="image/*" @required(!$portfolio->profile_picture_path)><small>JPG, PNG, WebP or another image format · up to 5 MB</small>@if ($pictureUrl)<img class="form-avatar" src="{{ $pictureUrl }}" alt="Current profile picture">@endif @error('profile_picture')<small class="field-error">{{ $message }}</small>@enderror</label>
            <label class="field field-wide"><span>About me <b>*</b></span><textarea name="about_me" rows="4" maxlength="5000" placeholder="A few lines about what you care about and what you do." required>{{ old('about_me', $portfolio->about_me) }}</textarea><small>Keep it focused. A short introduction is easy to read.</small>@error('about_me')<small class="field-error">{{ $message }}</small>@enderror</label>
        </div>
    </section>

    <section class="form-section repeat-section">
        <div class="section-heading"><span class="section-index">02</span><div><h2>Education</h2><p>Add a school, course or qualification.</p></div></div>
        <div class="repeat-list">
            @foreach ($educationRows as $index => $item)
                <div class="repeat-card">
                    <button class="remove-row" type="button" aria-label="Remove education item">×</button>
                    <div class="form-grid">
                        <label class="field"><span>School or institution</span><input name="education[{{ $index }}][institution]" value="{{ $item['institution'] ?? '' }}" maxlength="180" placeholder="School or organization"></label>
                        <label class="field"><span>Qualification</span><input name="education[{{ $index }}][qualification]" value="{{ $item['qualification'] ?? '' }}" maxlength="180" placeholder="Degree, diploma or course"></label>
                        <label class="field"><span>Field of study</span><input name="education[{{ $index }}][field_of_study]" value="{{ $item['field_of_study'] ?? '' }}" maxlength="180" placeholder="e.g. Information Management"></label>
                        <label class="field"><span>Start date</span><input type="date" name="education[{{ $index }}][start_date]" value="{{ $item['start_date'] ?? '' }}"></label>
                        <label class="field"><span>End date</span><input type="date" name="education[{{ $index }}][end_date]" value="{{ $item['end_date'] ?? '' }}"></label>
                        <label class="field field-wide"><span>Notes</span><textarea name="education[{{ $index }}][description]" rows="2" maxlength="2000" placeholder="Optional details">{{ $item['description'] ?? '' }}</textarea></label>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="add-row" type="button" data-add-item>＋ Add education</button>
        <template><div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove education item">×</button><div class="form-grid"><label class="field"><span>School or institution</span><input name="education[__INDEX__][institution]" maxlength="180" placeholder="School or organization"></label><label class="field"><span>Qualification</span><input name="education[__INDEX__][qualification]" maxlength="180" placeholder="Degree, diploma or course"></label><label class="field"><span>Field of study</span><input name="education[__INDEX__][field_of_study]" maxlength="180" placeholder="e.g. Information Management"></label><label class="field"><span>Start date</span><input type="date" name="education[__INDEX__][start_date]"></label><label class="field"><span>End date</span><input type="date" name="education[__INDEX__][end_date]"></label><label class="field field-wide"><span>Notes</span><textarea name="education[__INDEX__][description]" rows="2" maxlength="2000" placeholder="Optional details"></textarea></label></div></div></template>
    </section>

    <section class="form-section repeat-section">
        <div class="section-heading"><span class="section-index">03</span><div><h2>Skills</h2><p>Highlight strengths you want people to notice.</p></div></div>
        <div class="repeat-list">
            @foreach ($skillRows as $index => $item)
                <div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove skill">×</button><div class="form-grid"><label class="field"><span>Skill</span><input name="skills[{{ $index }}][name]" value="{{ $item['name'] ?? '' }}" maxlength="120" placeholder="e.g. Research"></label><label class="field"><span>Level or context</span><input name="skills[{{ $index }}][proficiency]" value="{{ $item['proficiency'] ?? '' }}" maxlength="60" placeholder="Optional"></label></div></div>
            @endforeach
        </div>
        <button class="add-row" type="button" data-add-item>＋ Add skill</button>
        <template><div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove skill">×</button><div class="form-grid"><label class="field"><span>Skill</span><input name="skills[__INDEX__][name]" maxlength="120" placeholder="e.g. Research"></label><label class="field"><span>Level or context</span><input name="skills[__INDEX__][proficiency]" maxlength="60" placeholder="Optional"></label></div></div></template>
    </section>

    <section class="form-section repeat-section">
        <div class="section-heading"><span class="section-index">04</span><div><h2>Projects</h2><p>Show work you are proud of. A link or screenshot is optional.</p></div></div>
        <div class="repeat-list">
            @foreach ($projectRows as $index => $item)
                <div class="repeat-card">
                    <button class="remove-row" type="button" aria-label="Remove project">×</button>
                    <div class="form-grid">
                        <label class="field"><span>Project name</span><input name="projects[{{ $index }}][title]" value="{{ $item['title'] ?? '' }}" maxlength="180" placeholder="Project name"></label>
                        <label class="field"><span>Project link</span><input type="url" name="projects[{{ $index }}][project_url]" value="{{ $item['project_url'] ?? '' }}" maxlength="2048" placeholder="https://"></label>
                        <label class="field"><span>Tags</span><input name="projects[{{ $index }}][tags]" value="{{ is_array($item['tags'] ?? null) ? implode(', ', $item['tags']) : ($item['tags'] ?? '') }}" maxlength="500" placeholder="e.g. Research, Web design"><small>Separate tags with commas.</small></label>
                        <label class="field field-wide"><span>Description</span><textarea name="projects[{{ $index }}][description]" rows="3" maxlength="3000" placeholder="What did you make, and what was your role?">{{ $item['description'] ?? '' }}</textarea></label>
                        <label class="field"><span>Project screenshot</span><input type="file" name="projects[{{ $index }}][screenshot]" accept="image/*"><small>Image up to 5 MB</small></label>
                        <input type="hidden" name="projects[{{ $index }}][id]" value="{{ $item['id'] ?? '' }}">
                        @if (!empty($item['screenshot_path']) && !empty($projectImageUrls[$item['screenshot_path']]))
                            <div class="field"><span>Current screenshot</span><img class="project-thumb" src="{{ $projectImageUrls[$item['screenshot_path']] }}" alt="Current project screenshot"></div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        <button class="add-row" type="button" data-add-item>＋ Add project</button>
        <template><div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove project">×</button><div class="form-grid"><label class="field"><span>Project name</span><input name="projects[__INDEX__][title]" maxlength="180" placeholder="Project name"></label><label class="field"><span>Project link</span><input type="url" name="projects[__INDEX__][project_url]" maxlength="2048" placeholder="https://"></label><label class="field"><span>Tags</span><input name="projects[__INDEX__][tags]" maxlength="500" placeholder="e.g. Research, Web design"><small>Separate tags with commas.</small></label><label class="field field-wide"><span>Description</span><textarea name="projects[__INDEX__][description]" rows="3" maxlength="3000" placeholder="What did you make, and what was your role?"></textarea></label><label class="field"><span>Project screenshot</span><input type="file" name="projects[__INDEX__][screenshot]" accept="image/*"><small>Image up to 5 MB</small></label><input type="hidden" name="projects[__INDEX__][id]" value=""></div></div></template>
    </section>

    <details class="form-section repeat-section secondary-section">
        <summary><div class="section-heading"><span class="section-index">05</span><div><h2>Work experience <small>Optional</small></h2><p>Add roles that support the story you want to tell.</p></div><span class="summary-caret">＋</span></div></summary>
        <div class="repeat-list">
            @foreach ($workRows as $index => $item)
                <div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove experience">×</button><div class="form-grid"><label class="field"><span>Role or position</span><input name="work_experience[{{ $index }}][role]" value="{{ $item['role'] ?? '' }}" maxlength="180" placeholder="Role"></label><label class="field"><span>Company or organization</span><input name="work_experience[{{ $index }}][company]" value="{{ $item['company'] ?? '' }}" maxlength="180" placeholder="Organization"></label><label class="field"><span>Start date</span><input type="date" name="work_experience[{{ $index }}][start_date]" value="{{ $item['start_date'] ?? '' }}"></label><label class="field"><span>End date</span><input type="date" name="work_experience[{{ $index }}][end_date]" value="{{ $item['end_date'] ?? '' }}"></label><label class="field field-wide"><span>What you did</span><textarea name="work_experience[{{ $index }}][description]" rows="3" maxlength="3000" placeholder="Optional details">{{ $item['description'] ?? '' }}</textarea></label></div></div>
            @endforeach
        </div>
        <button class="add-row" type="button" data-add-item>＋ Add experience</button>
        <template><div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove experience">×</button><div class="form-grid"><label class="field"><span>Role or position</span><input name="work_experience[__INDEX__][role]" maxlength="180" placeholder="Role"></label><label class="field"><span>Company or organization</span><input name="work_experience[__INDEX__][company]" maxlength="180" placeholder="Organization"></label><label class="field"><span>Start date</span><input type="date" name="work_experience[__INDEX__][start_date]"></label><label class="field"><span>End date</span><input type="date" name="work_experience[__INDEX__][end_date]"></label><label class="field field-wide"><span>What you did</span><textarea name="work_experience[__INDEX__][description]" rows="3" maxlength="3000" placeholder="Optional details"></textarea></label></div></div></template>
    </details>

    <details class="form-section repeat-section secondary-section">
        <summary><div class="section-heading"><span class="section-index">06</span><div><h2>Social and website links <small>Optional</small></h2><p>Link to places where people can see more of your work.</p></div><span class="summary-caret">＋</span></div></summary>
        <div class="repeat-list">
            @foreach ($socialRows as $index => $item)
                <div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove link">×</button><div class="form-grid"><label class="field"><span>Platform</span><input name="social_links[{{ $index }}][platform]" value="{{ $item['platform'] ?? '' }}" maxlength="80" placeholder="Website, LinkedIn, GitHub"></label><label class="field"><span>Display label</span><input name="social_links[{{ $index }}][label]" value="{{ $item['label'] ?? '' }}" maxlength="120" placeholder="My portfolio"></label><label class="field field-wide"><span>URL</span><input type="url" name="social_links[{{ $index }}][url]" value="{{ $item['url'] ?? '' }}" maxlength="2048" placeholder="https://"></label></div></div>
            @endforeach
        </div>
        <button class="add-row" type="button" data-add-item>＋ Add link</button>
        <template><div class="repeat-card"><button class="remove-row" type="button" aria-label="Remove link">×</button><div class="form-grid"><label class="field"><span>Platform</span><input name="social_links[__INDEX__][platform]" maxlength="80" placeholder="Website, LinkedIn, GitHub"></label><label class="field"><span>Display label</span><input name="social_links[__INDEX__][label]" maxlength="120" placeholder="My portfolio"></label><label class="field field-wide"><span>URL</span><input type="url" name="social_links[__INDEX__][url]" maxlength="2048" placeholder="https://"></label></div></div></template>
    </details>

    <div class="form-actions"><a class="button button-quiet" href="{{ $portfolio->exists ? route('portfolios.show', $portfolio) : route('home') }}">Cancel</a><button class="button button-quiet" type="submit" name="save_for_later" value="1">Save for later</button><button class="button button-primary" type="submit">{{ $portfolio->exists ? 'Save changes' : 'Save and choose a template' }}</button></div>
</form>
@endsection
