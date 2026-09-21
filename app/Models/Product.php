<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use App\Stevebauman\Inventory\Traits\InventoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use Hashidable;
    use InteractsWithMedia;
    use InventoryTrait;
    use LogsActivity;
    use SoftDeletes;

    protected $table = 'inventories';

    protected $dates = ['deleted_at'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'name_kh',
        'description',
        'cost',
        'price',
        'expiry_date',
        'is_use_stock',
        'user_updated',
        'pbar_code',
        'whole_sale_price',
        'membership_price',
        'branch_id',
        'is_change',
        'main_cat_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'category_id',
                'name',
                'name_kh',
                'description',
                'cost',
                'price',
                'expiry_date',
                'is_use_stock',
                'user_updated',
                'pbar_code',
                'whole_sale_price',
                'membership_price',
                'branch_id',
                'is_change',
                'main_cat_id',
            ]);
    }

    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    public function main_category()
    {
        return $this->hasOne('App\Models\MainCategory', 'id', 'main_cat_id');
    }

    public function user_create()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function user_update()
    {
        return $this->belongsTo('App\Models\User', 'user_updated');
    }

    public function metrics()
    {
        return $this->morphToMany("App\Models\Metric", 'metricsables')->withPivot('metric_id', 'metricsables_id', 'metricsables_type', 'price', 'user_id')->orderBy('metrics.qty', 'desc');
    }

    /**
     * The hasOne metric relationship.
     *
     * @return HasOne
     */
    public function metric()
    {
        return $this->hasOne('App\Models\Metric', 'id', 'metric_id');
    }

    /**
     * The hasOne sku relationship.
     *
     * @return HasOne
     */
    public function sku()
    {
        return $this->hasOne('App\Stevebauman\Inventory\Models\InventorySku', 'inventory_id', 'id');
    }

    /**
     * The hasMany stocks relationship.
     *
     * @return HasMany
     */
    public function stocks()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryStock', 'inventory_id', 'id');
    }

    /**
     * The belongsToMany suppliers relationship.
     *
     * @return BelongsToMany
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Models\Supplier', 'inventory_suppliers', 'inventory_id')->withTimestamps();
    }

    // /**
    //  * The belongsToMany assemblies relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  */
    // public function assemblies()
    // {
    //     return $this->belongsToMany($this, 'inventory_assemblies', 'inventory_id', 'part_id')->withPivot(['quantity'])->withTimestamps();
    // }

    // public function invoiceItem()
    // {
    //     return $this->hasOne("HUY\Sale\Entities\InvoiceItem");
    // }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function remarks()
    {
        return $this->morphToMany("App\Models\Remark", 'remarksables')->withPivot('remark_id', 'remarksables_id', 'remarksables_type');
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('inventories.branch_id', $branch);
        }
    }

    //
}
