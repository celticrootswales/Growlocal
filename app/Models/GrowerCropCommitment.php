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

    public function grower()
    {
        return $this->belongsTo(User::class, 'grower_id');
    }

    public function distributorNeed()
    {
        return $this->belongsTo(DistributorCropNeed::class, 'distributor_crop_need_id');
    }    

    public function cropOffering()
    {
        return $this->hasOneThrough(
            CropOffering::class,
            DistributorCropNeed::class,
            'id', // Foreign key on DistributorCropNeed
            'id', // Foreign key on CropOffering
            'distributor_crop_need_id', // Local key on this model
            'crop_offering_id' // Local key on DistributorCropNeed
        );
    }

    public function distributorCropNeed()
    {
        return $this->belongsTo(DistributorCropNeed::class);
    }


}