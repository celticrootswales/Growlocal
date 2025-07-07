<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeeklyEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_crop_plan_id',
        'grower_id',
        'estimated_quantity',
        'notes',
    ];


    public function grower()
    {
        return $this->belongsTo(User::class, 'grower_id');
    }

    public function allocation()
    {
        return $this->belongsTo(WeeklyAllocation::class, 'weekly_allocation_id');
    }
    public function weeklyCropPlan()
    {
        return $this->belongsTo(WeeklyCropPlan::class, 'weekly_crop_plan_id');
    }
}