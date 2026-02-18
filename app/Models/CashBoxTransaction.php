<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBoxTransaction extends Model
{
    protected $fillable = [
        'cash_box_id',
        'type',
        'amount',
        'description',
        'reference_id',
        'reference_type',
        'user_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function cashBox(): BelongsTo
    {
        return $this->belongsTo(CashBox::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        $prefix = $this->type === 'in' ? '+' : '-';
        return $prefix . ' ' . number_format($this->amount, 2) . ' ج.م';
    }

    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeByCashBox($query, $cashBoxId)
    {
        return $query->where('cash_box_id', $cashBoxId);
    }
}
