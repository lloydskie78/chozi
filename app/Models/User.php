<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'wallet_balance',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'wallet_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Role constants
    const ROLE_RENTER = 'renter';
    const ROLE_OWNER = 'owner';
    const ROLE_BROKER = 'broker';
    const ROLE_ADMIN = 'admin';

    // Relationships
    public function choziCodes()
    {
        return $this->hasMany(ChoziCode::class, 'broker_id');
    }

    public function sentPayments()
    {
        return $this->hasMany(Payment::class, 'payer_id');
    }

    public function receivedPayments()
    {
        return $this->hasMany(Payment::class, 'recipient_id');
    }

    public function brokerCommissions()
    {
        return $this->hasMany(Payment::class, 'broker_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function isBroker()
    {
        return $this->role === self::ROLE_BROKER;
    }

    public function isOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isRenter()
    {
        return $this->role === self::ROLE_RENTER;
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getActiveChoziCode()
    {
        return $this->choziCodes()->where('is_active', true)->first();
    }
}
