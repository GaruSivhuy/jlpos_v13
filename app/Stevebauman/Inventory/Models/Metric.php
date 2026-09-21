<?php

namespace App\Stevebauman\Inventory\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Metric.
 */
class Metric extends BaseModel
{
    protected $table = 'metrics';

    /**
     * The hasMany inventory items relationship.
     *
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\Inventory', 'metric_id', 'id');
    }
}
