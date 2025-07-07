<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GrowerCropCommitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'grower_id',
        'distributor_crop_need_id',
        'committed_quantity',
        'notes',
    ];

    public function grower() {
    return $this->belongsTo(User::class, 'grower_id');
    }
    public function distributorNeed() {
        return $this->belongsTo(DistributorCropNeed::class, 'distributor_crop_need_id');
    }

    // Access the crop offering via distributorCropNeed
   public function cropOffering()
    {
        return $this->hasOneThrough(
            CropOffering::class,
            DistributorCropNeed::class,
            'id',
            'id',
            'distributor_crop_need_id',
            'crop_offering_id'
        );
    }

    public function weeklyAllocations()
    {
        return $this->hasMany(WeeklyAllocation::class);
    }

    public function weeklyPlans()
    {
        return $this->hasMany(WeeklyCropPlan::class);
    }

    public function weeklyCropPlan()
    {
        return $this->belongsTo(\App\Models\WeeklyCropPlan::class);
    }
}