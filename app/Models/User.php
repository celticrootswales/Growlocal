<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'business_name',
        'phone',
        'location',
    ];

    /**
     * The attributes that should be hidden for arrays and JSON serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be typecast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the delivery notes for the user.
     */
    public function deliveryNotes()
    {
        return $this->hasMany(\App\Models\DeliveryNote::class);
    }

    public function cropOfferings()
    {
        return $this->belongsToMany(CropOffering::class, 'crop_offering_distributor', 'distributor_id', 'crop_offering_id');
    }
    
    /**
     * Distributors this grower is linked to.
     */
    public function distributors()
    {
        return $this->belongsToMany(User::class, 'distributor_grower', 'grower_id', 'distributor_id');
    }

    /**
     * Commitments this grower has made.
     */
    public function cropCommitments()
    {
        return $this->hasMany(GrowerCropCommitment::class);
    }

    public function assignedDistributors()
    {
        return $this->belongsToMany(User::class, 'distributor_grower', 'grower_id', 'distributor_id');
    }

    public function assignedGrowers()
    {
        return $this->belongsToMany(User::class, 'distributor_grower', 'distributor_id', 'grower_id');
    }
}