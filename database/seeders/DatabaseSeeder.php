<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache before seeding
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->call([
            PermissionSeeder::class,    // First create permissions
            RoleSeeder::class,           // Then create roles
            AdminSeeder::class,          // Then create admin users
            // ExamTablesSeeder::class,     // Temporarily commented out due to dependency issues
            GradeSystemSeeder::class,    // Then create grade system
            FinancePermissionsSeeder::class, // Add finance permissions
            SubjectPermissionsSeeder::class, // Add subject management permissions
            AssignmentPermissionsSeeder::class, // Add assignment permissions
            RolePermissionUpdate::class,  // Add/update role permissions for academic dean, examiner, accountant
            
            // Analytics and reporting seeders (Temporarily commented out)
            // ReportTemplateSeeder::class,    // Create report templates
            // DashboardWidgetSeeder::class,   // Create dashboard widgets
            // DefaultDashboardSeeder::class,  // Create default role-specific dashboards
        ]);
    }
}