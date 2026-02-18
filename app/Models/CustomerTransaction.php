<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class CustomerTransaction extends Model
{
    protected $fillable = [
        'customer_id',
        'type', // 'payment', 'sale', 'return', 'adjustment'
        'amount',
        'description',
        'transaction_date',
        'reference_id', // invoice_id or other reference
        'reference_type', // 'invoice', 'manual', 'wallet'
        'method', // 'cash', 'wallet', 'bank'
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Filter fillable fields based on actual table columns
        static::saving(function ($model) {
            $fillable = ['customer_id', 'type', 'amount'];
            
            if (Schema::hasColumn('customer_transactions', 'description')) {
                $fillable[] = 'description';
            }
            
            if (Schema::hasColumn('customer_transactions', 'transaction_date')) {
                $fillable[] = 'transaction_date';
            }
            
            if (Schema::hasColumn('customer_transactions', 'reference_id')) {
                $fillable[] = 'reference_id';
            }
            
            if (Schema::hasColumn('customer_transactions', 'reference_type')) {
                $fillable[] = 'reference_type';
            }
            
            if (Schema::hasColumn('customer_transactions', 'method')) {
                $fillable[] = 'method';
            }
            
            $model->fillable($fillable);
        });
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'reference_id')->where('reference_type', 'invoice');
    }
    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopeReturns($query)
    {
        return $query->where('type', 'return');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        // Use transaction_date if exists, otherwise created_at
        if (Schema::hasColumn('customer_transactions', 'transaction_date')) {
            return $query->whereBetween('transaction_date', [$startDate, $endDate]);
        } else {
            return $query->whereBetween('created_at', [$startDate, $endDate]);
        }
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeLatestTransaction($query)
    {
        // Use transaction_date if exists, otherwise created_at
        if (Schema::hasColumn('customer_transactions', 'transaction_date')) {
            return $query->latest('transaction_date');
        } else {
            return $query->latest('created_at');
        }
    }

    // Accessor for transaction date with fallback
    public function getTransactionDateAttribute($value)
    {
        return $value ?? $this->created_at;
    }
}
