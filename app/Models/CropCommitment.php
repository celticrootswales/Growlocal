<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropCommitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'crop_plan_id',
        'grower_id',
        'quantity_committed',
    ];

    public function plan()
    {
        return $this->belongsTo(CropPlan::class, 'crop_plan_id');
    }

    public function grower()
    {
        return $this->belongsTo(User::class, 'grower_id');
    }
    
    public function distributorCropNeed()
    {
        return $this->belongsTo(\App\Models\DistributorCropNeed::class);
    }
}