<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyCropPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'grower_crop_commitment_id',
        'week',
        'expected_quantity',
    ];

    public function commitment()
    {
        return $this->belongsTo(GrowerCropCommitment::class, 'grower_crop_commitment_id');
    }
}