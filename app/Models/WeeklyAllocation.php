<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyAllocation extends Model
{
    protected $fillable = [
        'grower_crop_commitment_id',
        'planned_date',
        'quantity',
    ];

    protected $casts = [
        'planned_date' => 'date',
    ];

    // Fix relation to WeeklyEstimate with correct FK name
    public function estimate()
    {
        return $this->hasOne(WeeklyEstimate::class, 'weekly_crop_plan_id', 'id')
            ->where('grower_id', auth()->id());
    }

    public function commitment()
    {
        return $this->belongsTo(GrowerCropCommitment::class, 'grower_crop_commitment_id');
    }

    public function cropPlan()
    {
        return $this->belongsTo(WeeklyCropPlan::class, 'grower_crop_commitment_id', 'grower_crop_commitment_id')
            ->whereDate('week', DB::raw('date(planned_date, "weekday 0", "-6 days")'));
    }
}