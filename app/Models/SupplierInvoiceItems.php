<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierInvoiceItems extends Model
{
    protected $fillable  = 
    [
        'supplier_invoice_id',
        'product_id',
        'quantity',
        'unit_price'
    ];

    public function product(){
    return $this->belongsTo(Product::class);
}
}
