<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_reference',
        'payer_id',
        'recipient_id',
        'broker_id',
        'chozi_code_id',
        'amount',
        'broker_commission',
        'net_amount',
        'status',
        'payment_type',
        'description',
        'property_details',
        'payment_method',
        'processed_at',
        'transaction_hash',
        'security_metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'broker_commission' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'security_metadata' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment type constants
    const TYPE_RENT = 'rent';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_MAINTENANCE = 'maintenance';
    const TYPE_OTHER = 'other';

    // Relationships
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function broker()
    {
        return $this->belongsTo(User::class, 'broker_id');
    }

    public function choziCode()
    {
        return $this->belongsTo(ChoziCode::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Helper methods
    public function generatePaymentReference()
    {
        do {
            $reference = 'PAY' . strtoupper(Str::random(12));
        } while (self::where('payment_reference', $reference)->exists());
        
        $this->payment_reference = $reference;
        return $reference;
    }

    public function calculateCommission($commissionRate = 5.00)
    {
        $this->broker_commission = ($this->amount * $commissionRate) / 100;
        $this->net_amount = $this->amount - $this->broker_commission;
    }

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->processed_at = now();
        $this->save();
    }

    public function markAsFailed()
    {
        $this->status = self::STATUS_FAILED;
        $this->save();
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function canBeProcessed()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByPayer($query, $payerId)
    {
        return $query->where('payer_id', $payerId);
    }

    public function scopeByRecipient($query, $recipientId)
    {
        return $query->where('recipient_id', $recipientId);
    }

    public function scopeByBroker($query, $brokerId)
    {
        return $query->where('broker_id', $brokerId);
    }
}
