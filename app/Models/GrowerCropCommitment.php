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

    public function distributorCropNeed()
    {
        return $this->belongsTo(DistributorCropNeed::class);
    }
}