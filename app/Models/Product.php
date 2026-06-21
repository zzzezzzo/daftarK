<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'price_base',
        'price_trade',
        'price_technician',
        'price_customer',
        'category_id',
        'stock'
    ];

    protected $casts = [
        'price_base' => 'decimal:2',
        'price_trade' => 'decimal:2',
        'price_technician' => 'decimal:2',
        'price_customer' => 'decimal:2',
        'stock' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get price based on customer type
     */
    public function getPriceForCustomerType($customerType)
    {
        // If category has specific rate, use rate-based calculation
        if ($this->category && $this->category->priceRate) {
            $basePrice = $this->getBasePrice();
            return $this->category->calculatePriceForCustomerType($basePrice, $customerType);
        }

        // Fallback to direct price fields
        return match($customerType) {
        'trade'     => (float) $this->price_trade,
        'technical' => (float) $this->price_technician,
        'client'    => (float) $this->price_customer,
        default     => (float) $this->price_customer,
    };
    }

    /**
     * Get base price (using client price as base)
     */
    private function getBasePrice()
    {
        return $this->price_base;
    }

    /**
     * Get formatted price for customer type
     */
    public function getFormattedPriceForCustomerType($customerType)
    {
        $price = $this->getPriceForCustomerType($customerType);
        return number_format($price, 2);
    }

    /**
     * Get all prices for display
     */
    public function getAllPrices()
    {
        return [
            'trade' => $this->getPriceForCustomerType('trade'),
            'technical' => $this->getPriceForCustomerType('technical'),
            'client' => $this->getPriceForCustomerType('client'),
        ];
    }
    public function saleItems()
    {
        return $this->hasMany(CustomerInvoiceItems::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(SupplierInvoiceItems::class);
    }
}
