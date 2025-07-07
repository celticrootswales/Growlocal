<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryBox extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'crop',
        'quantity',
        'label_code',
        'crop_offering_id',
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(\App\Models\DeliveryNote::class, 'delivery_note_id');
    }

    public function grower()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function note()
    {
        return $this->belongsTo(\App\Models\DeliveryNote::class, 'delivery_note_id');
    }
    public function cropOffering()
    {
        return $this->belongsTo(\App\Models\CropOffering::class, 'crop_offering_id');
    }
}