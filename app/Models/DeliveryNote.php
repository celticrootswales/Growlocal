<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'destination', 'traceability_number', 'status',
        'invoice_path', 'recalled', 'recall_acknowledged'
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
}