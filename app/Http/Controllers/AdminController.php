<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display users management page
     */
    public function users(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        
        // Role filter
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        
        // Order by latest first
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get summary statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'renters' => User::where('role', 'renter')->count(),
            'owners' => User::where('role', 'owner')->count(),
            'brokers' => User::where('role', 'broker')->count(),
            'admins' => User::where('role', 'admin')->count(),
        ];
        
        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Show create user form
     */
    public function createUser()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function storeUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:renter,owner,broker,admin'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'wallet_balance' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'wallet_balance' => $request->wallet_balance ?? 0.00,
            'address' => $request->address,
            'is_active' => true,
        ]);

        // Log the action
        Transaction::log(
            Transaction::TYPE_PROFILE_UPDATE,
            'User created by admin',
            "Admin created new user: {$user->name} ({$user->email})",
            [
                'created_user_id' => $user->id,
                'created_user_role' => $user->role,
                'admin_id' => auth()->id(),
            ],
            auth()->user(),
            null,
            Transaction::SEVERITY_INFO
        );

        return redirect()->route('admin.users')->with('success', 'User created successfully!');
    }

    /**
     * Show edit user form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'in:renter,owner,broker,admin'],
            'wallet_balance' => ['nullable', 'numeric', 'min:0'],
            'address' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldData = $user->toArray();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role = $request->role;
        $user->wallet_balance = $request->wallet_balance ?? $user->wallet_balance;
        $user->address = $request->address;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Log the action
        Transaction::log(
            Transaction::TYPE_PROFILE_UPDATE,
            'User updated by admin',
            "Admin updated user: {$user->name} ({$user->email})",
            [
                'updated_user_id' => $user->id,
                'old_data' => $oldData,
                'new_data' => $user->toArray(),
                'admin_id' => auth()->id(),
            ],
            auth()->user(),
            null,
            Transaction::SEVERITY_INFO
        );

        return redirect()->route('admin.users')->with('success', 'User updated successfully!');
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(User $user)
    {
        // Prevent admin from deactivating themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $oldStatus = $user->is_active;
        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'activated' : 'deactivated';
        
        // Log the action
        Transaction::log(
            Transaction::TYPE_PROFILE_UPDATE,
            "User {$action} by admin",
            "Admin {$action} user: {$user->name} ({$user->email})",
            [
                'affected_user_id' => $user->id,
                'old_status' => $oldStatus,
                'new_status' => $user->is_active,
                'admin_id' => auth()->id(),
            ],
            auth()->user(),
            null,
            Transaction::SEVERITY_WARNING
        );

        return back()->with('success', "User {$action} successfully!");
    }

    /**
     * Delete user (soft delete concept)
     */
    public function deleteUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Instead of actual deletion, we'll deactivate and log
        $user->is_active = false;
        $user->email = $user->email . '_deleted_' . time(); // Prevent email conflicts
        $user->save();

        // Log the action
        Transaction::log(
            Transaction::TYPE_PROFILE_UPDATE,
            'User deleted by admin',
            "Admin deleted user: {$user->name}",
            [
                'deleted_user_id' => $user->id,
                'deleted_user_data' => $user->toArray(),
                'admin_id' => auth()->id(),
            ],
            auth()->user(),
            null,
            Transaction::SEVERITY_CRITICAL
        );

        return back()->with('success', 'User deleted successfully!');
    }
} 