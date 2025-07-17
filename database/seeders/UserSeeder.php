<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ChoziCode;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin User
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@chozipay.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '+254700000000',
            'wallet_balance' => 100000.00,
            'is_active' => true,
            'last_login_at' => now(),
        ]);

        // Property Owners
        $owners = [
            [
                'name' => 'John Kamau',
                'email' => 'john.kamau@gmail.com',
                'phone' => '+254701234567',
                'wallet_balance' => 50000.00,
            ],
            [
                'name' => 'Mary Wanjiku',
                'email' => 'mary.wanjiku@yahoo.com',
                'phone' => '+254702345678',
                'wallet_balance' => 75000.00,
            ],
            [
                'name' => 'David Otieno',
                'email' => 'david.otieno@gmail.com',
                'phone' => '+254703456789',
                'wallet_balance' => 25000.00,
            ],
        ];

        foreach ($owners as $ownerData) {
            User::create([
                'name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => $ownerData['phone'],
                'wallet_balance' => $ownerData['wallet_balance'],
                'is_active' => true,
                'last_login_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        // Renters
        $renters = [
            [
                'name' => 'Sarah Muthoni',
                'email' => 'sarah.muthoni@gmail.com',
                'phone' => '+254704567890',
                'wallet_balance' => 15000.00,
            ],
            [
                'name' => 'Peter Kipchoge',
                'email' => 'peter.kipchoge@outlook.com',
                'phone' => '+254705678901',
                'wallet_balance' => 8000.00,
            ],
            [
                'name' => 'Grace Akinyi',
                'email' => 'grace.akinyi@gmail.com',
                'phone' => '+254706789012',
                'wallet_balance' => 12000.00,
            ],
            [
                'name' => 'Michael Mwangi',
                'email' => 'michael.mwangi@yahoo.com',
                'phone' => '+254707890123',
                'wallet_balance' => 5000.00,
            ],
            [
                'name' => 'Jennifer Nyong\'o',
                'email' => 'jennifer.nyongo@gmail.com',
                'phone' => '+254708901234',
                'wallet_balance' => 18000.00,
            ],
        ];

        foreach ($renters as $renterData) {
            User::create([
                'name' => $renterData['name'],
                'email' => $renterData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'renter',
                'phone' => $renterData['phone'],
                'wallet_balance' => $renterData['wallet_balance'],
                'is_active' => true,
                'last_login_at' => now()->subDays(rand(1, 15)),
            ]);
        }

        // Brokers with ChoziCodes
        $brokers = [
            [
                'name' => 'Robert Kiprotich',
                'email' => 'robert.kiprotich@chozipay.com',
                'phone' => '+254709012345',
                'wallet_balance' => 8500.00,
                'chozi_codes' => ['BROKER001', 'WEST2024', 'KILI001']
            ],
            [
                'name' => 'Susan Wanjiru',
                'email' => 'susan.wanjiru@chozipay.com',
                'phone' => '+254710123456',
                'wallet_balance' => 12000.00,
                'chozi_codes' => ['BROKER002', 'EAST2024', 'KASA001']
            ],
            [
                'name' => 'James Ochieng',
                'email' => 'james.ochieng@chozipay.com',
                'phone' => '+254711234567',
                'wallet_balance' => 6750.00,
                'chozi_codes' => ['BROKER003', 'SOUTH2024', 'KAR001']
            ],
            [
                'name' => 'Lucy Nyambura',
                'email' => 'lucy.nyambura@chozipay.com',
                'phone' => '+254712345678',
                'wallet_balance' => 15000.00,
                'chozi_codes' => ['BROKER004', 'NORTH2024', 'RUA001']
            ],
        ];

        foreach ($brokers as $brokerData) {
            $broker = User::create([
                'name' => $brokerData['name'],
                'email' => $brokerData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'broker',
                'phone' => $brokerData['phone'],
                'wallet_balance' => $brokerData['wallet_balance'],
                'is_active' => true,
                'last_login_at' => now()->subDays(rand(1, 7)),
            ]);

            // Create ChoziCodes for each broker
            foreach ($brokerData['chozi_codes'] as $codeValue) {
                ChoziCode::create([
                    'code' => $codeValue,
                    'broker_id' => $broker->id,
                    'commission_rate' => 5.00, // 5% commission
                    'is_active' => true,
                    'usage_count' => rand(5, 50), // Random usage count
                ]);
            }
        }

        // Demo users for testing
        $demoUsers = [
            [
                'name' => 'Demo Renter',
                'email' => 'demo.renter@chozipay.com',
                'role' => 'renter',
                'phone' => '+254700000001',
                'wallet_balance' => 20000.00,
            ],
            [
                'name' => 'Demo Owner',
                'email' => 'demo.owner@chozipay.com',
                'role' => 'owner',
                'phone' => '+254700000002',
                'wallet_balance' => 100000.00,
            ],
            [
                'name' => 'Demo Broker',
                'email' => 'demo.broker@chozipay.com',
                'role' => 'broker',
                'phone' => '+254700000003',
                'wallet_balance' => 10000.00,
            ],
        ];

        foreach ($demoUsers as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('demo123'),
                'role' => $userData['role'],
                'phone' => $userData['phone'],
                'wallet_balance' => $userData['wallet_balance'],
                'is_active' => true,
                'last_login_at' => now(),
            ]);

            // Create a demo ChoziCode for the demo broker
            if ($userData['role'] === 'broker') {
                ChoziCode::create([
                    'code' => 'DEMO2024',
                    'broker_id' => $user->id,
                    'commission_rate' => 5.00,
                    'is_active' => true,
                    'usage_count' => 25,
                ]);
            }
        }
    }
}
