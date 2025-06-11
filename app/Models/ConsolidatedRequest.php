<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsolidatedRequest extends Model
{
    //
    protected $guarded = [];

    protected $casts = [
        'item_code' => 'array',
    ];
}
