<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin
        User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@chovaynhanh.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
            'permissions' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'loans.view',
                'loans.approve',
                'loans.reject',
                'loans.edit',
                'payments.view',
                'payments.process',
                'reports.view',
                'settings.view',
                'settings.edit',
                'system.manage'
            ]
        ]);

        // Create loan officer
        User::create([
            'name' => 'Loan Officer',
            'username' => 'loan_officer',
            'email' => 'loan@chovaynhanh.com',
            'password' => Hash::make('loan123'),
            'role' => 'admin',
            'status' => 'active',
            'permissions' => [
                'users.view',
                'loans.view',
                'loans.approve',
                'loans.reject',
                'loans.edit',
                'payments.view',
                'reports.view'
            ]
        ]);

        // Create customer service
        User::create([
            'name' => 'Customer Service',
            'username' => 'cs_staff',
            'email' => 'cs@chovaynhanh.com',
            'password' => Hash::make('cs123'),
            'role' => 'admin',
            'status' => 'active',
            'permissions' => [
                'users.view',
                'loans.view',
                'payments.view'
            ]
        ]);
    }
}