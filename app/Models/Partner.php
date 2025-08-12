<?php

namespace App\Models;

use App\Traits\GuardHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Partner extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, GuardHelpers;

    /**
     * The guard associated with the model.
     *
     * @var string
     */
    protected $guard_name = 'partner';

    protected $fillable = [
        'partner_id',
        'name',
        'email',
        'password',
        'company_name',
        'phone',
        'address',
        'contact_person',
        'website',
        'profile_photo',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Generate unique partner ID
     */
    public static function generatePartnerId(): string
    {
        $lastPartner = self::latest('id')->first();
        $lastId = $lastPartner ? (int) substr($lastPartner->partner_id, 3) : 0;
        return 'PTR' . str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }
}
