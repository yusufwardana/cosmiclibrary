<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Update extends Model
{
    use HasFactory;

    protected $fillable = [
        'version', 'channel', 'release_url', 'checksum',
        'size_bytes', 'changelog', 'status', 'log', 'applied_at',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'applied_at' => 'datetime',
    ];

    public function scopePending($q): void
    {
        $q->where('status', 'pending');
    }

    public function scopeApplied($q): void
    {
        $q->where('status', 'applied');
    }
}