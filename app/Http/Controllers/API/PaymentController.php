<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\ChoziCode;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Process a rental payment
     */
    public function processPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'recipient_email' => ['required', 'email', 'exists:users,email'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
            'chozi_code' => ['nullable', 'string', 'exists:chozi_codes,code'],
            'payment_type' => ['required', 'in:rent,deposit,maintenance,other'],
            'description' => ['nullable', 'string', 'max:1000'],
            'property_details' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $payer = Auth::user();
        $recipient = User::where('email', $request->recipient_email)->first();

        // Security checks
        if ($payer->id === $recipient->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot send payment to yourself'
            ], 400);
        }

        if (!$payer->is_active || !$recipient->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive'
            ], 400);
        }

        // Check wallet balance (simulated)
        if ($payer->wallet_balance < $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient wallet balance'
            ], 400);
        }

        // Validate and process ChoziCode if provided
        $choziCode = null;
        $broker = null;
        if ($request->chozi_code) {
            $choziCode = ChoziCode::where('code', $request->chozi_code)
                ->active()
                ->valid()
                ->first();

            if (!$choziCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired ChoziCode'
                ], 400);
            }

            $broker = $choziCode->broker;
        }

        // Start database transaction
        DB::beginTransaction();

        try {
            // Create payment record
            $payment = new Payment();
            $payment->generatePaymentReference();
            $payment->payer_id = $payer->id;
            $payment->recipient_id = $recipient->id;
            $payment->amount = $request->amount;
            $payment->payment_type = $request->payment_type;
            $payment->description = $request->description;
            $payment->property_details = $request->property_details;
            $payment->status = Payment::STATUS_PROCESSING;

            // Process ChoziCode and calculate commission
            if ($choziCode && $broker) {
                $payment->chozi_code_id = $choziCode->id;
                $payment->broker_id = $broker->id;
                $payment->calculateCommission($choziCode->commission_rate);
                
                // Update ChoziCode usage
                $choziCode->incrementUsage();
            } else {
                $payment->broker_commission = 0;
                $payment->net_amount = $payment->amount;
            }

            // Security metadata
            $payment->security_metadata = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'timestamp' => now()->toISOString(),
            ];

            // Generate transaction hash for security
            $payment->transaction_hash = hash('sha256', 
                $payment->payment_reference . 
                $payment->amount . 
                $payment->payer_id . 
                $payment->recipient_id . 
                now()->timestamp
            );

            $payment->save();

            // Process wallet transactions (simulated)
            $payer->wallet_balance -= $payment->amount;
            $payer->save();

            $recipient->wallet_balance += $payment->net_amount;
            $recipient->save();

            if ($broker && $payment->broker_commission > 0) {
                $broker->wallet_balance += $payment->broker_commission;
                $broker->save();
            }

            // Mark payment as completed
            $payment->markAsCompleted();

            // Log transactions
            Transaction::logPayment($payment, 'Payment initiated', 
                "Payment of {$payment->amount} from {$payer->name} to {$recipient->name}");

            if ($choziCode) {
                Transaction::logChoziCodeUsage($choziCode, $payment);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment_reference' => $payment->payment_reference,
                    'amount' => $payment->amount,
                    'broker_commission' => $payment->broker_commission,
                    'net_amount' => $payment->net_amount,
                    'status' => $payment->status,
                    'recipient' => [
                        'name' => $recipient->name,
                        'email' => $recipient->email,
                    ],
                    'chozi_code' => $choziCode ? $choziCode->code : null,
                    'broker' => $broker ? $broker->name : null,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            Transaction::log(
                Transaction::TYPE_PAYMENT,
                'Payment failed',
                "Payment processing failed: {$e->getMessage()}",
                [
                    'amount' => $request->amount,
                    'recipient_email' => $request->recipient_email,
                    'error' => $e->getMessage()
                ],
                $payer,
                null,
                Transaction::SEVERITY_ERROR
            );

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get payment history
     */
    public function getPaymentHistory(Request $request)
    {
        $user = Auth::user();
        $perPage = min($request->get('per_page', 15), 50);

        $query = Payment::query()
            ->where(function ($q) use ($user) {
                $q->where('payer_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
                  
                if ($user->isBroker()) {
                    $q->orWhere('broker_id', $user->id);
                }
            })
            ->with(['payer:id,name,email', 'recipient:id,name,email', 'broker:id,name,email', 'choziCode:id,code'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('payment_type', $request->type);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    /**
     * Get payment details
     */
    public function getPaymentDetails($paymentReference)
    {
        $user = Auth::user();
        
        $payment = Payment::where('payment_reference', $paymentReference)
            ->where(function ($q) use ($user) {
                $q->where('payer_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
                  
                if ($user->isBroker()) {
                    $q->orWhere('broker_id', $user->id);
                }
            })
            ->with(['payer:id,name,email', 'recipient:id,name,email', 'broker:id,name,email', 'choziCode:id,code'])
            ->first();

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment
        ]);
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats()
    {
        $user = Auth::user();

        $stats = [
            'total_sent' => $user->sentPayments()->completed()->sum('amount'),
            'total_received' => $user->receivedPayments()->completed()->sum('net_amount'),
            'pending_payments' => $user->sentPayments()->pending()->count() + $user->receivedPayments()->pending()->count(),
            'completed_payments' => $user->sentPayments()->completed()->count() + $user->receivedPayments()->completed()->count(),
        ];

        if ($user->isBroker()) {
            $stats['total_commissions'] = $user->brokerCommissions()->completed()->sum('broker_commission');
            $stats['active_chozi_codes'] = $user->choziCodes()->active()->count();
        }

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
