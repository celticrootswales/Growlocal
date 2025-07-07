<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'distributor_id',
        'traceability_number',
        'status',
        'invoice_path',
        'recalled',
        'recall_acknowledged',
    ];

    // Relationship: DeliveryNote belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional alias for user (grower)
    public function grower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cropOffering()
    {
        return $this->belongsTo(\App\Models\CropOffering::class, 'crop_offering_id');
    }

    // One-to-Many: DeliveryNote has many DeliveryBoxes
    public function boxes()
    {
        return $this->hasMany(DeliveryBox::class);
    }

    // One-to-One: A DeliveryNote may have one recall record
    public function recall()
    {
        return $this->hasOne(Recall::class, 'delivery_note_id');
    }

    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }
}