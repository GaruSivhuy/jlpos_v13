<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use Hashidable;

    protected $table = 'branch';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name_en',
        'name_kh',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
