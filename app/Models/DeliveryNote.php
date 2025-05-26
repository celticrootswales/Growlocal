<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'destination',
        'invoice_path',
        'traceability_number',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function boxes()
    {
        return $this->hasMany(DeliveryBox::class);
    }
}