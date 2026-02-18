<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'type',
        'price_type'
    ];
    public function invoices(){
        return $this->hasMany(CustomerInvoice::class);
    }
    public function wallet(){
        return $this->hasOne(CustomerWallet::class);
    }
    public function transactions(){
        return $this->hasMany(CustomerTransaction::class);
    }
}
