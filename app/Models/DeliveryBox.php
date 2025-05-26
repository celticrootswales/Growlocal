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
        return $this->belongsTo(DeliveryNote::class);
    }
}