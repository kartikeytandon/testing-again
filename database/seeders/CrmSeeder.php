<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ModuleAccess;
use App\Models\Order;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\SalesReport;
use App\Models\LedgerRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CrmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'address' => '123 Admin Street, City',
                'is_active' => true,
            ]
        );

        // Create Sales Person
        $salesPerson = User::updateOrCreate(
            ['email' => 'sales@example.com'],
            [
                'name' => 'John Sales',
                'password' => Hash::make('password123'),
                'role' => 'sales_person',
                'phone' => '+1234567891',
                'address' => '456 Sales Avenue, City',
                'is_active' => true,
            ]
        );

        // Create Office Staff
        $officeStaff = User::updateOrCreate(
            ['email' => 'office@example.com'],
            [
                'name' => 'Jane Office',
                'password' => Hash::make('password123'),
                'role' => 'office_staff',
                'phone' => '+1234567892',
                'address' => '789 Office Road, City',
                'is_active' => true,
            ]
        );

        // Create Client
        $client = User::updateOrCreate(
            ['email' => 'client@example.com'],
            [
                'name' => 'Client Company',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => '+1234567893',
                'address' => '321 Client Boulevard, City',
                'is_active' => true,
            ]
        );

        // Create Module Access for Admin
        ModuleAccess::updateOrCreate(
            ['user_id' => $admin->id, 'module_name' => 'user_management'],
            [
                'permissions' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $admin->id, 'module_name' => 'order_management'],
            [
                'permissions' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $admin->id, 'module_name' => 'ledger_management'],
            [
                'permissions' => ['view', 'create', 'edit', 'delete', 'approve', 'export'],
                'is_active' => true,
            ]
        );

        // Create Module Access for Sales Person
        ModuleAccess::updateOrCreate(
            ['user_id' => $salesPerson->id, 'module_name' => 'order_management'],
            [
                'permissions' => ['view', 'create', 'edit'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $salesPerson->id, 'module_name' => 'sales_reporting'],
            [
                'permissions' => ['view', 'create', 'edit', 'export'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $salesPerson->id, 'module_name' => 'attendance'],
            [
                'permissions' => ['view', 'create'],
                'is_active' => true,
            ]
        );

        // Create Module Access for Office Staff
        ModuleAccess::updateOrCreate(
            ['user_id' => $officeStaff->id, 'module_name' => 'task_management'],
            [
                'permissions' => ['view', 'create', 'edit'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $officeStaff->id, 'module_name' => 'attendance'],
            [
                'permissions' => ['view', 'create'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $officeStaff->id, 'module_name' => 'order_management'],
            [
                'permissions' => ['view', 'edit'],
                'is_active' => true,
            ]
        );

        // Create Module Access for Client
        ModuleAccess::updateOrCreate(
            ['user_id' => $client->id, 'module_name' => 'order_management'],
            [
                'permissions' => ['view', 'create'],
                'is_active' => true,
            ]
        );

        ModuleAccess::updateOrCreate(
            ['user_id' => $client->id, 'module_name' => 'ledger_management'],
            [
                'permissions' => ['view', 'create'],
                'is_active' => true,
            ]
        );

        // Create Sample Orders
        Order::create([
            'client_id' => $client->id,
            'order_details' => 'Laptop computers - 10 units of Dell Latitude 5520',
            'remark' => 'Urgent delivery required for new office setup',
            'status' => 'pending',
        ]);

        Order::create([
            'client_id' => $client->id,
            'order_details' => 'Office furniture - 5 desks and 10 chairs',
            'remark' => 'Standard delivery',
            'status' => 'ordered_with_supplier',
        ]);

        // Create Sample Tasks
        Task::create([
            'user_id' => $salesPerson->id,
            'task_title' => 'Follow up with client',
            'task_description' => 'Call client regarding order status and delivery timeline',
            'task_date' => now()->toDateString(),
            'priority' => 'high',
            'due_date' => now()->addDays(2)->toDateString(),
            'remarks' => 'Important client - prioritize',
            'status' => 'pending',
        ]);

        Task::create([
            'user_id' => $officeStaff->id,
            'task_title' => 'Update inventory',
            'task_description' => 'Update stock levels for all products',
            'task_date' => now()->toDateString(),
            'priority' => 'medium',
            'due_date' => now()->addDays(1)->toDateString(),
            'remarks' => 'Monthly task',
            'status' => 'pending',
        ]);

        // Create Sample Attendance
        Attendance::create([
            'user_id' => $salesPerson->id,
            'attendance_date' => now()->toDateString(),
            'check_in_time' => now()->setTime(9, 0)->toTimeString(),
            'check_out_time' => now()->setTime(17, 30)->toTimeString(),
            'location' => 'Office',
            'status' => 'present',
        ]);

        Attendance::create([
            'user_id' => $officeStaff->id,
            'attendance_date' => now()->toDateString(),
            'check_in_time' => now()->setTime(8, 30)->toTimeString(),
            'location' => 'Office',
            'status' => 'present',
        ]);

        // Create Sample Sales Report
        SalesReport::create([
            'sales_person_id' => $salesPerson->id,
            'client_id' => $client->id,
            'visit_date' => now()->subDays(1)->toDateString(),
            'order_value' => 5000.00,
            'remarks' => 'Successful product demonstration',
            'visit_duration' => 60,
            'visit_purpose' => 'Product demonstration',
            'client_feedback' => 'Very interested in the solution',
            'follow_up_required' => true,
            'follow_up_date' => now()->addDays(7)->toDateString(),
        ]);

        // Create Sample Ledger Request
        LedgerRequest::create([
            'client_id' => $client->id,
            'request_date' => now()->toDateString(),
            'status' => 'pending',
        ]);
    }
}
