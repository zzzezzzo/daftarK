<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoice extends Model
{
    //
    protected $fillable = [
        'supplier_id',
        'type',
        'date',
        'total_amount',
        'paid_amount',
        'Remaining_amount',
        'states',
        'invoice_number'
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'Remaining_amount' => 'decimal:2',
    ];
    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
    public function items(){
        return $this->hasMany(SupplierInvoiceItems::class);
    }
    // SupplierInvoice.php
public function payment(){
    return $this->hasOne(SupplierPayment::class);
}

}
