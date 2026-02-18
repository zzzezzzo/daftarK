<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
    ];
    
    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }
    
    public function getBalanceAttribute()
    {
        $deposits = $this->transactions()->deposits()->sum('amount');
        $withdrawals = $this->transactions()->withdrawals()->sum('amount');
        return $deposits - $withdrawals;
    }
}
