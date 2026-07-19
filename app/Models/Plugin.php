<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    protected $fillable = [
        'slug', 'name', 'version', 'description', 'author',
        'hook', 'settings', 'is_active', 'priority',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public function scopeActive($q): void
    {
        $q->where('is_active', true);
    }
}