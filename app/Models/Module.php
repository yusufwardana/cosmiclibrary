<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug', 'name', 'version', 'description', 'provider',
        'priority', 'dependencies', 'compatibility', 'status',
    ];

    protected $casts = [
        'dependencies' => 'array',
        'compatibility' => 'array',
        'priority' => 'integer',
    ];

    public function scopeActive($q): void
    {
        $q->where('status', 'active');
    }

    public function scopeInstalled($q): void
    {
        $q->whereIn('status', ['installed', 'active']);
    }
}