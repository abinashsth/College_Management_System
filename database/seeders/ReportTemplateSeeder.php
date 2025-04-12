<?php

namespace Database\Seeders;

use App\Models\Reporting\ReportTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            // Academic Reports
            [
                'name' => 'Academic Performance Overview',
                'report_type' => 'academic',
                'description' => 'A comprehensive overview of academic performance metrics including GPA, pass rates, and attendance.',
                'parameters' => [
                    [
                        'name' => 'academic_year_id',
                        'type' => 'select',
                        'label' => 'Academic Year',
                        'required' => true,
                    ],
                    [
                        'name' => 'academic_term_id',
                        'type' => 'select',
                        'label' => 'Academic Term',
                        'required' => false,
                    ],
                    [
                        'name' => 'department_id',
                        'type' => 'select',
                        'label' => 'Department',
                        'required' => false,
                    ],
                ],
                'filters' => [
                    'course_id' => 'Course',
                    'program_id' => 'Program',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'pie'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Financial Reports
            [
                'name' => 'Financial Summary',
                'report_type' => 'financial',
                'description' => 'A summary of financial data including revenue, expenses, outstanding fees, and payments.',
                'parameters' => [
                    [
                        'name' => 'start_date',
                        'type' => 'date',
                        'label' => 'Start Date',
                        'required' => true,
                    ],
                    [
                        'name' => 'end_date',
                        'type' => 'date',
                        'label' => 'End Date',
                        'required' => true,
                    ],
                ],
                'filters' => [
                    'department_id' => 'Department',
                    'fee_category_id' => 'Fee Category',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'pie'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Attendance Reports
            [
                'name' => 'Attendance Report',
                'report_type' => 'attendance',
                'description' => 'A detailed report on student attendance patterns and statistics.',
                'parameters' => [
                    [
                        'name' => 'start_date',
                        'type' => 'date',
                        'label' => 'Start Date',
                        'required' => true,
                    ],
                    [
                        'name' => 'end_date',
                        'type' => 'date',
                        'label' => 'End Date',
                        'required' => true,
                    ],
                    [
                        'name' => 'class_id',
                        'type' => 'select',
                        'label' => 'Class',
                        'required' => false,
                    ],
                ],
                'filters' => [
                    'section_id' => 'Section',
                    'subject_id' => 'Subject',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'heatmap'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Examination Reports
            [
                'name' => 'Examination Results Analysis',
                'report_type' => 'examination',
                'description' => 'A comprehensive analysis of examination results including grade distribution and performance metrics.',
                'parameters' => [
                    [
                        'name' => 'exam_id',
                        'type' => 'select',
                        'label' => 'Examination',
                        'required' => true,
                    ],
                ],
                'filters' => [
                    'department_id' => 'Department',
                    'class_id' => 'Class',
                    'section_id' => 'Section',
                    'subject_id' => 'Subject',
                ],
                'exports' => ['pdf', 'excel'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'pie', 'histogram'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Staff Reports
            [
                'name' => 'Staff Performance Report',
                'report_type' => 'staff',
                'description' => 'A performance report for teaching and non-teaching staff.',
                'parameters' => [
                    [
                        'name' => 'academic_year_id',
                        'type' => 'select',
                        'label' => 'Academic Year',
                        'required' => true,
                    ],
                    [
                        'name' => 'department_id',
                        'type' => 'select',
                        'label' => 'Department',
                        'required' => false,
                    ],
                ],
                'filters' => [
                    'role' => 'Role',
                    'evaluation_type' => 'Evaluation Type',
                ],
                'exports' => ['pdf', 'excel'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'radar', 'line'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Student Reports
            [
                'name' => 'Student Progress Report',
                'report_type' => 'student',
                'description' => 'A detailed report on student academic progress over time.',
                'parameters' => [
                    [
                        'name' => 'student_id',
                        'type' => 'select',
                        'label' => 'Student',
                        'required' => true,
                    ],
                    [
                        'name' => 'academic_year_id',
                        'type' => 'select',
                        'label' => 'Academic Year',
                        'required' => false,
                    ],
                ],
                'filters' => [
                    'subject_id' => 'Subject',
                ],
                'exports' => ['pdf'],
                'visualization_options' => [
                    'chart_types' => ['line', 'bar', 'radar'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Admission Reports
            [
                'name' => 'Admission Statistics',
                'report_type' => 'admission',
                'description' => 'Statistical analysis of admissions including demographics, program popularity, and conversion rates.',
                'parameters' => [
                    [
                        'name' => 'academic_year_id',
                        'type' => 'select',
                        'label' => 'Academic Year',
                        'required' => true,
                    ],
                ],
                'filters' => [
                    'program_id' => 'Program',
                    'department_id' => 'Department',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'pie', 'line', 'funnel'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Comparison Reports
            [
                'name' => 'Academic Year Comparison',
                'report_type' => 'comparison',
                'description' => 'Compare key metrics between two academic years.',
                'parameters' => [
                    [
                        'name' => 'academic_session_1',
                        'type' => 'select',
                        'label' => 'First Academic Year',
                        'required' => true,
                    ],
                    [
                        'name' => 'academic_session_2',
                        'type' => 'select',
                        'label' => 'Second Academic Year',
                        'required' => true,
                    ],
                    [
                        'name' => 'metric_slug',
                        'type' => 'select',
                        'label' => 'Metric to Compare',
                        'required' => true,
                        'options' => [
                            'student_performance' => 'Student Performance',
                            'attendance_rate' => 'Attendance Rate',
                            'revenue' => 'Revenue',
                            'expenses' => 'Expenses',
                            'admission_count' => 'Admission Count',
                        ],
                    ],
                ],
                'filters' => [
                    'department_id' => 'Department',
                    'program_id' => 'Program',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'radar'],
                    'color_schemes' => ['default', 'comparison', 'cool', 'warm'],
                ],
                'is_active' => true,
            ],
            
            // Custom Reports
            [
                'name' => 'Custom Report Builder',
                'report_type' => 'custom',
                'description' => 'Create custom reports by selecting metrics, dimensions, and filters.',
                'parameters' => [
                    [
                        'name' => 'report_name',
                        'type' => 'string',
                        'label' => 'Report Name',
                        'required' => true,
                    ],
                    [
                        'name' => 'metrics',
                        'type' => 'array',
                        'label' => 'Metrics',
                        'required' => true,
                    ],
                    [
                        'name' => 'dimensions',
                        'type' => 'array',
                        'label' => 'Dimensions',
                        'required' => false,
                    ],
                    [
                        'name' => 'start_date',
                        'type' => 'date',
                        'label' => 'Start Date',
                        'required' => false,
                    ],
                    [
                        'name' => 'end_date',
                        'type' => 'date',
                        'label' => 'End Date',
                        'required' => false,
                    ],
                ],
                'filters' => [
                    'department_id' => 'Department',
                    'program_id' => 'Program',
                    'class_id' => 'Class',
                    'section_id' => 'Section',
                ],
                'exports' => ['pdf', 'excel', 'csv'],
                'visualization_options' => [
                    'chart_types' => ['bar', 'line', 'pie', 'scatter', 'radar', 'heatmap'],
                    'color_schemes' => ['default', 'monochrome', 'cool', 'warm', 'custom'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($templates as $templateData) {
            $template = new ReportTemplate();
            $template->name = $templateData['name'];
            $template->slug = Str::slug($templateData['name']);
            $template->description = $templateData['description'];
            $template->report_type = $templateData['report_type'];
            $template->parameters = $templateData['parameters'];
            $template->filters = $templateData['filters'];
            $template->export_formats = $templateData['exports'];
            $template->visualization_options = $templateData['visualization_options'];
            $template->is_active = $templateData['is_active'];
            $template->save();
        }
    }
} 