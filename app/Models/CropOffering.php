<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CropOffering extends Model
{
    protected $fillable = [
	    'crop_name',
	    'icon',
	    'unit',
	    'year',
	    'default_price',
	    'amount_needed',
	    'term',
	];

    public function distributors()
	{
	    return $this->belongsToMany(User::class, 'crop_offering_distributor', 'crop_offering_id', 'distributor_id');
	}

	public function growers()
	{
	    return $this->belongsToMany(User::class, 'distributor_grower', 'distributor_id', 'grower_id')
	        ->whereHas('roles', fn($q) => $q->where('name', 'grower'));
	}

	public function growerCommitments()
	{
	    return $this->hasManyThrough(
	        GrowerCropCommitment::class,
	        DistributorCropNeed::class,
	        'crop_offering_id',              // Foreign key on DistributorCropNeed
	        'distributor_crop_need_id',      // Foreign key on GrowerCropCommitment
	        'id',                            // Local key on CropOffering
	        'id'                             // Local key on DistributorCropNeed
	    );
	}
	public function distributorNeeds()
	{
	    return $this->hasMany(DistributorCropNeed::class);
	}
}