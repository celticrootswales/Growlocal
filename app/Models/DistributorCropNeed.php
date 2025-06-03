<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DistributorCropNeed extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'crop_offering_id',
        'desired_quantity',
        'notes',
    ];

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function cropOffering()
    {
        return $this->belongsTo(CropOffering::class);
    }
}