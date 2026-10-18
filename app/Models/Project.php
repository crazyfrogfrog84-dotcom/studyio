<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = ['title', 'description', 'project_url', 'screenshot_path', 'tags', 'sort_order'];

    protected function casts(): array
    {
        return ['tags' => 'array'];
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}
