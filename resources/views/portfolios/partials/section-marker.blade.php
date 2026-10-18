<span class="preview-section-index {{ $templateSlug === 'modern' ? 'preview-section-index-icon' : '' }}" aria-hidden="true">
    @if ($templateSlug === 'modern')
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
            @switch($icon)
                @case('projects')
                    <rect x="3" y="3" width="8" height="8" rx="1" /><rect x="13" y="3" width="8" height="5" rx="1" /><rect x="13" y="10" width="8" height="11" rx="1" /><rect x="3" y="13" width="8" height="8" rx="1" />
                    @break
                @case('education')
                    <path d="m2 9 10-5 10 5-10 5L2 9Z" /><path d="M6 11v5c3.5 3 8.5 3 12 0v-5M22 9v6" />
                    @break
                @case('skills')
                    <path d="M12 3v3m0 12v3M3 12h3m12 0h3M5.64 5.64l2.12 2.12m8.48 8.48 2.12 2.12m0-12.72-2.12 2.12m-8.48 8.48-2.12 2.12" /><circle cx="12" cy="12" r="4" />
                    @break
                @case('work')
                    <rect x="3" y="7" width="18" height="14" rx="2" /><path d="M8 7V4h8v3M3 12h18m-11 0v2h4v-2" />
                    @break
                @case('contact')
                    <path d="M4 5h16v12H9l-5 3V5Z" /><path d="M8 10h8m-8 4h5" />
                    @break
            @endswitch
        </svg>
    @else
        {{ $number }}
    @endif
</span>
