<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoiceItems extends Model
{
    protected $fillable = [
        'customer_invoice_id',
        'product_id',
        'quantity',
        'unit_price'
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }   
    public function invoice()
    {
        return $this->belongsTo(CustomerInvoice::class, 'customer_invoice_id');
    }    
}
