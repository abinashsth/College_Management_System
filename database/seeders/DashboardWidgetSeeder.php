<?php

namespace Database\Seeders;

use App\Models\Dashboard\DashboardWidget;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DashboardWidgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $widgets = [
            // Counter widgets
            [
                'name' => 'Total Students',
                'widget_type' => 'counter',
                'description' => 'Display the total number of students',
                'data_source' => [
                    'type' => 'metric',
                    'metric_slug' => 'total_students',
                ],
                'display_options' => [
                    'icon' => 'users',
                    'color' => 'blue',
                    'trend' => true,
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Super Admin', 'Admin', 'Teacher'],
            ],
            [
                'name' => 'Total Revenue',
                'widget_type' => 'counter',
                'description' => 'Display the total revenue',
                'data_source' => [
                    'type' => 'metric',
                    'metric_slug' => 'total_revenue',
                ],
                'display_options' => [
                    'icon' => 'currency-dollar',
                    'color' => 'green',
                    'trend' => true,
                    'format' => 'currency',
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Super Admin', 'Admin', 'Accountant'],
            ],
            [
                'name' => 'Attendance Rate',
                'widget_type' => 'counter',
                'description' => 'Display the average attendance rate',
                'data_source' => [
                    'type' => 'metric',
                    'metric_slug' => 'attendance_rate',
                ],
                'display_options' => [
                    'icon' => 'check-circle',
                    'color' => 'teal',
                    'trend' => true,
                    'format' => 'percentage',
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Super Admin', 'Admin', 'Teacher'],
            ],
            
            // Chart widgets
            [
                'name' => 'Student Enrollment Trend',
                'widget_type' => 'chart',
                'description' => 'Chart showing student enrollment trends over time',
                'data_source' => [
                    'type' => 'time_series',
                    'metric_slug' => 'student_enrollment',
                    'time_period' => 'month',
                ],
                'display_options' => [
                    'chart_type' => 'line',
                    'color_scheme' => 'blue',
                    'show_legend' => true,
                    'height' => 300,
                ],
                'refresh_interval' => 86400, // 24 hours
                'roles' => ['Super Admin', 'Admin'],
            ],
            [
                'name' => 'Fee Collection Status',
                'widget_type' => 'chart',
                'description' => 'Pie chart showing fee collection status',
                'data_source' => [
                    'type' => 'aggregate',
                    'dimensions' => ['payment_status'],
                    'metric_slug' => 'fee_amount',
                ],
                'display_options' => [
                    'chart_type' => 'pie',
                    'color_scheme' => 'categorical',
                    'show_legend' => true,
                    'height' => 300,
                ],
                'refresh_interval' => 43200, // 12 hours
                'roles' => ['Super Admin', 'Admin', 'Accountant'],
            ],
            [
                'name' => 'Grade Distribution',
                'widget_type' => 'chart',
                'description' => 'Bar chart showing grade distribution',
                'data_source' => [
                    'type' => 'aggregate',
                    'dimensions' => ['grade'],
                    'metric_slug' => 'student_count',
                ],
                'display_options' => [
                    'chart_type' => 'bar',
                    'color_scheme' => 'green',
                    'show_legend' => false,
                    'height' => 300,
                ],
                'refresh_interval' => 86400, // 24 hours
                'roles' => ['Super Admin', 'Admin', 'Teacher'],
            ],
            
            // Table widgets
            [
                'name' => 'Recent Fee Payments',
                'widget_type' => 'table',
                'description' => 'Table showing recent fee payments',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'payments',
                    'order_by' => 'created_at',
                    'order_direction' => 'desc',
                    'limit' => 10,
                ],
                'display_options' => [
                    'columns' => ['student', 'amount', 'payment_method', 'date'],
                    'pagination' => false,
                    'search' => false,
                ],
                'refresh_interval' => 300, // 5 minutes
                'roles' => ['Super Admin', 'Admin', 'Accountant'],
            ],
            [
                'name' => 'Upcoming Examinations',
                'widget_type' => 'table',
                'description' => 'Table showing upcoming examinations',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'exams',
                    'condition' => 'start_date > now()',
                    'order_by' => 'start_date',
                    'order_direction' => 'asc',
                    'limit' => 5,
                ],
                'display_options' => [
                    'columns' => ['title', 'start_date', 'end_date', 'status'],
                    'pagination' => false,
                    'search' => false,
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Super Admin', 'Admin', 'Teacher', 'Student'],
            ],
            
            // Calendar widget
            [
                'name' => 'Academic Calendar',
                'widget_type' => 'calendar',
                'description' => 'Calendar showing academic events',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'events',
                    'condition' => 'category = "academic"',
                ],
                'display_options' => [
                    'view_mode' => 'month',
                    'color_by_category' => true,
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Super Admin', 'Admin', 'Teacher', 'Student'],
            ],
            
            // List widget
            [
                'name' => 'Recent Activities',
                'widget_type' => 'list',
                'description' => 'List of recent activities',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'activity_logs',
                    'order_by' => 'created_at',
                    'order_direction' => 'desc',
                    'limit' => 10,
                ],
                'display_options' => [
                    'icon' => true,
                    'timestamp' => true,
                    'user' => true,
                ],
                'refresh_interval' => 300, // 5 minutes
                'roles' => ['Super Admin', 'Admin'],
            ],
            
            // Comparison widgets
            [
                'name' => 'Year-to-Year Student Performance',
                'widget_type' => 'comparison',
                'description' => 'Compare student performance between years',
                'data_source' => [
                    'type' => 'comparison',
                    'metric_slug' => 'student_performance',
                    'comparison_type' => 'year',
                ],
                'display_options' => [
                    'chart_type' => 'bar',
                    'color_scheme' => 'comparison',
                    'show_change' => true,
                    'height' => 300,
                ],
                'refresh_interval' => 86400, // 24 hours
                'roles' => ['Super Admin', 'Admin', 'Teacher'],
            ],
            
            // Student-specific widgets
            [
                'name' => 'My Attendance',
                'widget_type' => 'chart',
                'description' => 'Chart showing student\'s attendance',
                'data_source' => [
                    'type' => 'time_series',
                    'metric_slug' => 'student_attendance',
                    'time_period' => 'month',
                    'context_user' => true,
                ],
                'display_options' => [
                    'chart_type' => 'line',
                    'color_scheme' => 'blue',
                    'show_legend' => false,
                    'height' => 250,
                ],
                'refresh_interval' => 86400, // 24 hours
                'roles' => ['Student'],
            ],
            [
                'name' => 'My Grades',
                'widget_type' => 'table',
                'description' => 'Table showing student\'s grades',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'student_grades',
                    'condition' => 'student_id = :user_id',
                    'order_by' => 'created_at',
                    'order_direction' => 'desc',
                ],
                'display_options' => [
                    'columns' => ['subject', 'exam', 'grade', 'date'],
                    'pagination' => false,
                    'search' => false,
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Student'],
            ],
            
            // Teacher-specific widgets
            [
                'name' => 'My Classes',
                'widget_type' => 'table',
                'description' => 'Table showing teacher\'s classes',
                'data_source' => [
                    'type' => 'query',
                    'entity' => 'teacher_classes',
                    'condition' => 'teacher_id = :user_id',
                    'order_by' => 'schedule',
                    'order_direction' => 'asc',
                ],
                'display_options' => [
                    'columns' => ['class', 'subject', 'schedule', 'students'],
                    'pagination' => false,
                    'search' => false,
                ],
                'refresh_interval' => 3600, // 1 hour
                'roles' => ['Teacher'],
            ],
        ];

        foreach ($widgets as $widgetData) {
            $widget = new DashboardWidget();
            $widget->name = $widgetData['name'];
            $widget->slug = Str::slug($widgetData['name']);
            $widget->description = $widgetData['description'];
            $widget->widget_type = $widgetData['widget_type'];
            $widget->data_source = $widgetData['data_source'];
            $widget->display_options = $widgetData['display_options'];
            $widget->refresh_interval = $widgetData['refresh_interval'];
            $widget->access_roles = $widgetData['roles'];
            $widget->is_active = true;
            $widget->save();
        }
    }
} 