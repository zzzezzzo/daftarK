<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBox extends Model
{
    protected $fillable = [
        'name',
        'description',
        'opening_balance',
        'current_balance',
        'total_in',
        'total_out',
        'status',
        'user_id'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'total_in' => 'decimal:2',
        'total_out' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashBoxTransaction::class);
    }

    public function getBalanceFormattedAttribute(): string
    {
        return number_format($this->current_balance, 2) . ' ج.م';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
