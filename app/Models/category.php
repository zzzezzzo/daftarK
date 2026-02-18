<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',

    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function priceRate()
    {
        return $this->hasOne(CategoryPriceRate::class);
    }

    /**
     * Get price rate for specific customer type
     */
    public function getPriceRateForCustomerType($customerType)
    {
        $rate = $this->priceRate;
        
        if (!$rate) {
            // Default rates if no specific rate is set
            return match($customerType) {
                'trade' => 100,      // Default: no change
                'technical' => 100,  // Default: no change
                'client' => 100,     // Default: no change
                default => 100
            };
        }

        return match($customerType) {
            'trade' => $rate->rate_trade,
            'technical' => $rate->rate_technician,
            'client' => $rate->rate_client,
            default => $rate->rate_client
        };
    }

    /**
     * Calculate price for customer type using category rates
     */
    public function calculatePriceForCustomerType($basePrice, $customerType)
    {
        $rateMultiplier = (float) $this->getPriceRateForCustomerType($customerType);

        return $basePrice + ($basePrice * ($rateMultiplier / 100));
    }
}
