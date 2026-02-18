<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class CustomerInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'date',
        'total_amount',
        'paid_amount',
        'remining_amount', 
        'state',
        'type',
    ];
    public function customer(){
        return $this->belongsTo(Customer::class);
    }
    public function items(){
        return $this->hasMany(CustomerInvoiceItems::class);
    }
    public function transactions(){
        return $this->hasMany(CustomerTransaction::class, 'reference_id')->where('reference_type', 'invoice');
    }
}
