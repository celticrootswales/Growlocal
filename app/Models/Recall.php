<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recall extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_note_id',
        'reason',
    ];

    public function note()
    {
        return $this->belongsTo(DeliveryNote::class, 'delivery_note_id');
    }
}