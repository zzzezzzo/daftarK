<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryPriceRate extends Model
{
    protected $fillable = [
        'category_id',
        'rate_trade',
        'rate_technician', 
        'rate_client'
    ];
    protected $casts = [
        'rate_trade' => 'decimal:2',
        'rate_technician' => 'decimal:2',
        'rate_client' => 'decimal:2'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    /**
     * Calculate price based on customer type and rate
     */
    public function calculatePrice($basePrice, $customerType)
    {
        $rate = $this->getRateForCustomerType($customerType);
        return $basePrice * ($rate / 100);
    }
    /**
     * Get rate multiplier for customer type
     */
    public function getRateForCustomerType($customerType)
    {
        return match($customerType) {
            'trade' => $this->rate_trade,
            'technical' => $this->rate_technician,
            'client' => $this->rate_client,
            default => $this->rate_client
        };
    }
}
