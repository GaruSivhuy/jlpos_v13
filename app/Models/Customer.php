<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Customer extends Model
{
    use LogsActivity;

    protected $table = 'customers';

    protected $fillable = [
    	"user_id",
    	"name_kh",
    	"name_en",
    	"phone_number",
    	"other_phone",
    	"address",
    	"email",
        "cus_code",
        "user_updated",
        "branch_id",
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([ 
                "user_id",
                "name_kh",
                "name_en",
                "phone_number",
                "other_phone",
                "address",
                "email",
                "cus_code",
                "user_updated",
                "branch_id",
            ]);
    }
    
    protected static $logOnlyDirty = true;

    public function createdBy(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function updatedBy(){
        return $this->belongsTo('App\Models\User', 'user_updated');
    }
    
    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function scopeBranch($query){
        if(\Auth::user()->is_admin != 1){
            $branch = \Auth::user()->branch->pluck('id');
            return $query->whereIn('branch_id', $branch);
        }
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} customer";
    }
}
