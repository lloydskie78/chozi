<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ChoziCode;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChoziCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Generate a new ChoziCode for broker
     */
    public function generateCode(Request $request)
    {
        $user = Auth::user();

        // Only brokers can generate ChoziCodes
        if (!$user->isBroker()) {
            return response()->json([
                'success' => false,
                'message' => 'Only brokers can generate ChoziCodes'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'description' => ['nullable', 'string', 'max:500'],
            'commission_rate' => ['nullable', 'numeric', 'min:0.1', 'max:10.0'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if broker already has an active ChoziCode
        $existingCode = $user->choziCodes()->active()->first();
        if ($existingCode) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an active ChoziCode',
                'data' => [
                    'existing_code' => $existingCode->code,
                    'created_at' => $existingCode->created_at,
                    'usage_count' => $existingCode->usage_count,
                ]
            ], 400);
        }

        try {
            $choziCode = new ChoziCode();
            $choziCode->broker_id = $user->id;
            $choziCode->generateUniqueCode();
            $choziCode->commission_rate = $request->commission_rate ?? 5.00;
            $choziCode->description = $request->description;
            $choziCode->expires_at = $request->expires_at;
            $choziCode->is_active = true;
            $choziCode->save();

            // Log ChoziCode generation
            Transaction::log(
                Transaction::TYPE_CHOZI_CODE_GENERATION,
                'ChoziCode generated',
                "Broker {$user->name} generated ChoziCode: {$choziCode->code}",
                [
                    'code' => $choziCode->code,
                    'commission_rate' => $choziCode->commission_rate,
                    'expires_at' => $choziCode->expires_at,
                ],
                $user
            );

            return response()->json([
                'success' => true,
                'message' => 'ChoziCode generated successfully',
                'data' => [
                    'code' => $choziCode->code,
                    'commission_rate' => $choziCode->commission_rate,
                    'description' => $choziCode->description,
                    'expires_at' => $choziCode->expires_at,
                    'created_at' => $choziCode->created_at,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate ChoziCode',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Validate a ChoziCode
     */
    public function validateCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'string', 'size:8'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid code format',
                'errors' => $validator->errors()
            ], 422);
        }

        $choziCode = ChoziCode::where('code', $request->code)
            ->with('broker:id,name,email')
            ->first();

        if (!$choziCode) {
            return response()->json([
                'success' => false,
                'message' => 'ChoziCode not found'
            ], 404);
        }

        $isValid = $choziCode->isValid();

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $choziCode->code,
                'is_valid' => $isValid,
                'is_active' => $choziCode->is_active,
                'commission_rate' => $choziCode->commission_rate,
                'usage_count' => $choziCode->usage_count,
                'expires_at' => $choziCode->expires_at,
                'broker' => [
                    'name' => $choziCode->broker->name,
                    'email' => $choziCode->broker->email,
                ],
                'status' => $isValid ? 'valid' : 'invalid/expired'
            ]
        ]);
    }

    /**
     * Get broker's ChoziCodes
     */
    public function getBrokerCodes()
    {
        $user = Auth::user();

        if (!$user->isBroker()) {
            return response()->json([
                'success' => false,
                'message' => 'Only brokers can view ChoziCodes'
            ], 403);
        }

        $codes = $user->choziCodes()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($code) {
                return [
                    'id' => $code->id,
                    'code' => $code->code,
                    'commission_rate' => $code->commission_rate,
                    'usage_count' => $code->usage_count,
                    'total_commission_earned' => $code->getTotalCommissionEarned(),
                    'is_active' => $code->is_active,
                    'is_valid' => $code->isValid(),
                    'description' => $code->description,
                    'expires_at' => $code->expires_at,
                    'created_at' => $code->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $codes
        ]);
    }

    /**
     * Deactivate a ChoziCode
     */
    public function deactivateCode($codeId)
    {
        $user = Auth::user();

        if (!$user->isBroker()) {
            return response()->json([
                'success' => false,
                'message' => 'Only brokers can manage ChoziCodes'
            ], 403);
        }

        $choziCode = ChoziCode::where('id', $codeId)
            ->where('broker_id', $user->id)
            ->first();

        if (!$choziCode) {
            return response()->json([
                'success' => false,
                'message' => 'ChoziCode not found'
            ], 404);
        }

        $choziCode->is_active = false;
        $choziCode->save();

        Transaction::log(
            Transaction::TYPE_CHOZI_CODE_GENERATION,
            'ChoziCode deactivated',
            "Broker {$user->name} deactivated ChoziCode: {$choziCode->code}",
            [
                'code' => $choziCode->code,
                'usage_count' => $choziCode->usage_count,
                'total_commission' => $choziCode->getTotalCommissionEarned(),
            ],
            $user
        );

        return response()->json([
            'success' => true,
            'message' => 'ChoziCode deactivated successfully'
        ]);
    }

    /**
     * Get ChoziCode analytics for broker
     */
    public function getCodeAnalytics()
    {
        $user = Auth::user();

        if (!$user->isBroker()) {
            return response()->json([
                'success' => false,
                'message' => 'Only brokers can view analytics'
            ], 403);
        }

        $totalCodes = $user->choziCodes()->count();
        $activeCodes = $user->choziCodes()->active()->count();
        $totalUsage = $user->choziCodes()->sum('usage_count');
        $totalCommissions = $user->brokerCommissions()->completed()->sum('broker_commission');

        // Recent usage trends (last 30 days)
        $recentUsage = $user->choziCodes()
            ->withCount(['payments' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            }])
            ->get()
            ->pluck('payments_count', 'code')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total_codes' => $totalCodes,
                'active_codes' => $activeCodes,
                'total_usage' => $totalUsage,
                'total_commissions' => $totalCommissions,
                'average_commission_per_use' => $totalUsage > 0 ? ($totalCommissions / $totalUsage) : 0,
                'recent_usage_30_days' => $recentUsage,
            ]
        ]);
    }

    /**
     * Search for users by email (for payment purposes)
     */
    public function searchUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => ['required', 'string', 'min:3', 'max:50'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 3 characters',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->query;
        $currentUser = Auth::user();

        $users = User::where('is_active', true)
            ->where('id', '!=', $currentUser->id)
            ->where(function ($q) use ($query) {
                $q->where('email', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->select('id', 'name', 'email', 'role')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
