<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Portfolio extends Model
{
    protected $fillable = [
        'full_name',
        'profile_picture_path',
        'email',
        'contact_number',
        'address',
        'about_me',
        'template_id',
    ];

    public function education(): HasMany
    {
        return $this->hasMany(Education::class)->orderBy('sort_order')->orderBy('id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('sort_order')->orderBy('id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class)->orderBy('sort_order')->orderBy('id');
    }

    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class)->orderBy('sort_order')->orderBy('id');
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class)->orderBy('sort_order')->orderBy('id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
