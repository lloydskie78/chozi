<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_id',
        'transaction_type',
        'action',
        'description',
        'data',
        'ip_address',
        'user_agent',
        'session_id',
        'severity',
        'is_suspicious',
        'amount',
        'reference_id',
    ];

    protected $casts = [
        'data' => 'array',
        'is_suspicious' => 'boolean',
        'amount' => 'decimal:2',
    ];

    // Severity constants
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
    const SEVERITY_CRITICAL = 'critical';

    // Transaction type constants
    const TYPE_LOGIN = 'login';
    const TYPE_LOGOUT = 'logout';
    const TYPE_PAYMENT = 'payment';
    const TYPE_CHOZI_CODE_USAGE = 'chozi_code_usage';
    const TYPE_CHOZI_CODE_GENERATION = 'chozi_code_generation';
    const TYPE_FAILED_LOGIN = 'failed_login';
    const TYPE_PASSWORD_CHANGE = 'password_change';
    const TYPE_PROFILE_UPDATE = 'profile_update';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Helper methods
    public static function log($type, $action, $description = null, $data = null, $user = null, $payment = null, $severity = self::SEVERITY_INFO)
    {
        $transaction = new self();
        $transaction->transaction_type = $type;
        $transaction->action = $action;
        $transaction->description = $description;
        $transaction->data = $data;
        $transaction->severity = $severity;
        $transaction->user_id = $user ? $user->id : auth()->id();
        $transaction->payment_id = $payment ? $payment->id : null;
        
        // Capture security metadata
        if (request()) {
            $transaction->ip_address = request()->ip();
            $transaction->user_agent = request()->userAgent();
            $transaction->session_id = session()->getId();
        }
        
        $transaction->save();
        return $transaction;
    }

    public static function logLogin($user)
    {
        return self::log(
            self::TYPE_LOGIN,
            'User logged in',
            "User {$user->name} ({$user->email}) successfully logged in",
            ['user_id' => $user->id, 'role' => $user->role],
            $user
        );
    }

    public static function logFailedLogin($email)
    {
        return self::log(
            self::TYPE_FAILED_LOGIN,
            'Failed login attempt',
            "Failed login attempt for email: {$email}",
            ['email' => $email],
            null,
            null,
            self::SEVERITY_WARNING
        );
    }

    public static function logPayment($payment, $action, $description = null)
    {
        return self::log(
            self::TYPE_PAYMENT,
            $action,
            $description ?: "Payment {$action} for {$payment->payment_reference}",
            [
                'payment_reference' => $payment->payment_reference,
                'amount' => $payment->amount,
                'payer_id' => $payment->payer_id,
                'recipient_id' => $payment->recipient_id,
                'status' => $payment->status
            ],
            null,
            $payment
        );
    }

    public static function logChoziCodeUsage($choziCode, $payment)
    {
        return self::log(
            self::TYPE_CHOZI_CODE_USAGE,
            'ChoziCode used in payment',
            "ChoziCode {$choziCode->code} used in payment {$payment->payment_reference}",
            [
                'chozi_code' => $choziCode->code,
                'broker_id' => $choziCode->broker_id,
                'commission_rate' => $choziCode->commission_rate,
                'commission_amount' => $payment->broker_commission
            ],
            null,
            $payment
        );
    }

    public function markAsSuspicious($reason = null)
    {
        $this->is_suspicious = true;
        $this->severity = self::SEVERITY_WARNING;
        if ($reason) {
            $data = $this->data ?: [];
            $data['suspicious_reason'] = $reason;
            $this->data = $data;
        }
        $this->save();
    }

    // Scopes
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
