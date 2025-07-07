<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YearlyCommitment extends Model
{
    protected $fillable = [
        'grower_id',
        'crop_offering_id',
        'committed_quantity',
        'is_locked',
        // ...any others you use
    ];

    public function grower()
    {
        return $this->belongsTo(User::class, 'grower_id');
    }

    public function cropOffering()
    {
        return $this->belongsTo(CropOffering::class, 'crop_offering_id');
    }
}