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
    ];

    public function deliveryNote()
    {
        return $this->belongsTo(\App\Models\DeliveryNote::class, 'delivery_note_id');
    }

    public function grower()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}