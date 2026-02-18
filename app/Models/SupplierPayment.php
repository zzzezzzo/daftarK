<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierPayment extends Model
{
    protected $fillable = [
        'supplier_id',
        'supplier_invoice_id',
        'amount',
        'method',
        'payment_date'
    ];
}
