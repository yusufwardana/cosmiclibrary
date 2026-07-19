<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_key', 'domain', 'email', 'customer_name',
        'product', 'edition', 'status', 'activated_at',
        'expires_at', 'last_validated_at', 'meta',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_validated_at' => 'datetime',
        'meta' => 'array',
    ];

    public function scopeActive($q): void
    {
        $q->where('status', 'active');
    }

    public function scopeExpired($q): void
    {
        $q->where('status', 'expired');
    }

    public function isValid(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}