<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'sort_order'];

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }
}
