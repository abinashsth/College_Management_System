<?php

namespace Database\Seeders;

use App\Models\Dashboard\DashboardWidget;
use App\Models\Analytics\AnalyticsDashboard;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DefaultDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all roles that should have default dashboards
        $roles = Role::whereIn('name', [
            'Super Admin', 
            'Admin', 
            'Teacher',
            'Accountant',
            'Student'
        ])->get();
        
        foreach ($roles as $role) {
            $this->createRoleDashboard($role->name);
        }
    }
    
    /**
     * Create a default dashboard for a role.
     *
     * @param string $roleName
     * @return void
     */
    private function createRoleDashboard($roleName)
    {
        $dashboard = new AnalyticsDashboard();
        $dashboard->name = "{$roleName} Dashboard";
        $dashboard->slug = Str::slug("{$roleName} dashboard");
        $dashboard->description = "Default dashboard for {$roleName} role";
        $dashboard->is_default = true;
        $dashboard->is_shared = true;
        $dashboard->access_roles = [$roleName];
        
        // Set an empty layout that will be populated with widgets
        $dashboard->layout = [];
        $dashboard->save();
        
        // Attach widgets based on role
        $this->attachRoleWidgets($dashboard, $roleName);
    }
    
    /**
     * Attach appropriate widgets to a dashboard based on role.
     *
     * @param AnalyticsDashboard $dashboard
     * @param string $roleName
     * @return void
     */
    private function attachRoleWidgets($dashboard, $roleName)
    {
        // Get widgets appropriate for this role
        $widgets = DashboardWidget::whereJsonContains('access_roles', $roleName)
            ->where('is_active', true)
            ->get();
        
        // Position configuration for each role
        $positions = $this->getPositionConfigForRole($roleName);
        
        // Attach each widget with the proper positioning
        foreach ($widgets as $index => $widget) {
            // Use predefined positions or generate based on index
            $position = $positions[$widget->slug] ?? $this->generatePosition($index);
            
            // Create the widget instance
            $dashboard->dashboardWidgetInstances()->create([
                'widget_id' => $widget->id,
                'position_x' => $position['x'],
                'position_y' => $position['y'], 
                'width' => $position['w'],
                'height' => $position['h'],
                'instance_config' => $widget->default_config ?? null,
            ]);
        }
    }
    
    /**
     * Get preconfigured widget positions for a specific role.
     *
     * @param string $roleName
     * @return array
     */
    private function getPositionConfigForRole($roleName)
    {
        $positionConfigs = [
            'Super Admin' => [
                'total-students' => ['x' => 0, 'y' => 0, 'w' => 4, 'h' => 2],
                'total-revenue' => ['x' => 4, 'y' => 0, 'w' => 4, 'h' => 2],
                'attendance-rate' => ['x' => 8, 'y' => 0, 'w' => 4, 'h' => 2],
                'student-enrollment-trend' => ['x' => 0, 'y' => 2, 'w' => 8, 'h' => 4],
                'fee-collection-status' => ['x' => 8, 'y' => 2, 'w' => 4, 'h' => 4],
                'recent-activities' => ['x' => 0, 'y' => 6, 'w' => 6, 'h' => 4],
                'upcoming-examinations' => ['x' => 6, 'y' => 6, 'w' => 6, 'h' => 4],
                'year-to-year-student-performance' => ['x' => 0, 'y' => 10, 'w' => 12, 'h' => 4],
            ],
            'Admin' => [
                'total-students' => ['x' => 0, 'y' => 0, 'w' => 4, 'h' => 2],
                'total-revenue' => ['x' => 4, 'y' => 0, 'w' => 4, 'h' => 2],
                'attendance-rate' => ['x' => 8, 'y' => 0, 'w' => 4, 'h' => 2],
                'student-enrollment-trend' => ['x' => 0, 'y' => 2, 'w' => 8, 'h' => 4],
                'recent-activities' => ['x' => 0, 'y' => 6, 'w' => 6, 'h' => 4],
                'upcoming-examinations' => ['x' => 6, 'y' => 6, 'w' => 6, 'h' => 4],
            ],
            'Teacher' => [
                'total-students' => ['x' => 0, 'y' => 0, 'w' => 6, 'h' => 2],
                'attendance-rate' => ['x' => 6, 'y' => 0, 'w' => 6, 'h' => 2],
                'my-classes' => ['x' => 0, 'y' => 2, 'w' => 12, 'h' => 4],
                'grade-distribution' => ['x' => 0, 'y' => 6, 'w' => 6, 'h' => 4],
                'upcoming-examinations' => ['x' => 6, 'y' => 6, 'w' => 6, 'h' => 4],
                'year-to-year-student-performance' => ['x' => 0, 'y' => 10, 'w' => 12, 'h' => 4],
            ],
            'Accountant' => [
                'total-revenue' => ['x' => 0, 'y' => 0, 'w' => 6, 'h' => 2],
                'fee-collection-status' => ['x' => 0, 'y' => 2, 'w' => 6, 'h' => 4],
                'recent-fee-payments' => ['x' => 6, 'y' => 0, 'w' => 6, 'h' => 6],
            ],
            'Student' => [
                'my-attendance' => ['x' => 0, 'y' => 0, 'w' => 6, 'h' => 4],
                'my-grades' => ['x' => 6, 'y' => 0, 'w' => 6, 'h' => 4],
                'upcoming-examinations' => ['x' => 0, 'y' => 4, 'w' => 12, 'h' => 4],
                'academic-calendar' => ['x' => 0, 'y' => 8, 'w' => 12, 'h' => 5],
            ],
        ];
        
        return $positionConfigs[$roleName] ?? [];
    }
    
    /**
     * Generate a position for a widget based on its index.
     *
     * @param int $index
     * @return array
     */
    private function generatePosition($index)
    {
        // Simple grid layout: 2 columns, each widget 6 units wide
        $col = $index % 2;
        $row = floor($index / 2);
        
        return [
            'x' => $col * 6,
            'y' => $row * 4,
            'w' => 6,
            'h' => 4
        ];
    }
} 