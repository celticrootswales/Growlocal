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
}