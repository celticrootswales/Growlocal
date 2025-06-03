<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropPlan extends Model
{
    use HasFactory;

	protected $fillable = [
	    'week',
	    'crop_name',
	    'unit',
	    'expected_quantity',
	    'price_per_unit',
	    'distributor_id',
	    'grower_id',
	    'grower_estimate'
	];

    public function grower()
	{
	    return $this->belongsTo(User::class, 'grower_id');
	}

	public function distributor()
	{
	    return $this->belongsTo(User::class, 'distributor_id');
	}
	public function deliveredQuantity()
	{
	    return $this->grower
	        ? $this->grower->deliveryNotes()
	            ->whereDate('created_at', $this->week)
	            ->with('boxes')
	            ->get()
	            ->flatMap->boxes
	            ->where('crop', $this->crop_name)
	            ->sum('quantity')
	        : 0;
	}

	public function commitments()
	{
	    return $this->hasMany(\App\Models\CropCommitment::class, 'crop_plan_id');
	}


}