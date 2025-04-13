<?php

namespace Database\Seeders;

use App\Models\GradeSystem;
use App\Models\GradeScale;
use App\Models\User;
use Illuminate\Database\Seeder;

class GradeSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the first admin user to set as the creator
        $admin = User::role('Admin')->first();
        
        if (!$admin) {
            $admin = User::first(); // Fallback to first user if no admin exists
        }
        
        // Create default grade system
        $gradeSystem = GradeSystem::create([
            'name' => 'Standard Letter Grade System',
            'description' => 'Default letter grade system with A, B, C, D, F grades',
            'is_default' => true,
            'is_active' => true,
            'max_gpa' => 4.00,
            'pass_percentage' => 45.00,
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]);
        
        // Create grade scales
        $gradeScales = [
            [
                'grade' => 'A+',
                'description' => 'Outstanding',
                'min_percentage' => 95.00,
                'max_percentage' => 100.00,
                'grade_point' => 4.00,
                'remarks' => 'Outstanding',
                'is_fail' => false,
            ],
            [
                'grade' => 'A',
                'description' => 'Excellent',
                'min_percentage' => 90.00,
                'max_percentage' => 94.99,
                'grade_point' => 4.00,
                'remarks' => 'Excellent',
                'is_fail' => false,
            ],
            [
                'grade' => 'A-',
                'description' => 'Very Good',
                'min_percentage' => 85.00,
                'max_percentage' => 89.99,
                'grade_point' => 3.70,
                'remarks' => 'Very Good',
                'is_fail' => false,
            ],
            [
                'grade' => 'B+',
                'description' => 'Good',
                'min_percentage' => 80.00,
                'max_percentage' => 84.99,
                'grade_point' => 3.30,
                'remarks' => 'Good',
                'is_fail' => false,
            ],
            [
                'grade' => 'B',
                'description' => 'Above Average',
                'min_percentage' => 75.00,
                'max_percentage' => 79.99,
                'grade_point' => 3.00,
                'remarks' => 'Above Average',
                'is_fail' => false,
            ],
            [
                'grade' => 'B-',
                'description' => 'Average',
                'min_percentage' => 70.00,
                'max_percentage' => 74.99,
                'grade_point' => 2.70,
                'remarks' => 'Average',
                'is_fail' => false,
            ],
            [
                'grade' => 'C+',
                'description' => 'Below Average',
                'min_percentage' => 65.00,
                'max_percentage' => 69.99,
                'grade_point' => 2.30,
                'remarks' => 'Below Average',
                'is_fail' => false,
            ],
            [
                'grade' => 'C',
                'description' => 'Satisfactory',
                'min_percentage' => 60.00,
                'max_percentage' => 64.99,
                'grade_point' => 2.00,
                'remarks' => 'Satisfactory',
                'is_fail' => false,
            ],
            [
                'grade' => 'C-',
                'description' => 'Barely Satisfactory',
                'min_percentage' => 55.00,
                'max_percentage' => 59.99,
                'grade_point' => 1.70,
                'remarks' => 'Barely Satisfactory',
                'is_fail' => false,
            ],
            [
                'grade' => 'D+',
                'description' => 'Poor',
                'min_percentage' => 50.00,
                'max_percentage' => 54.99,
                'grade_point' => 1.30,
                'remarks' => 'Poor',
                'is_fail' => false,
            ],
            [
                'grade' => 'D',
                'description' => 'Very Poor',
                'min_percentage' => 45.00,
                'max_percentage' => 49.99,
                'grade_point' => 1.00,
                'remarks' => 'Very Poor',
                'is_fail' => false,
            ],
            [
                'grade' => 'F',
                'description' => 'Fail',
                'min_percentage' => 0.00,
                'max_percentage' => 44.99,
                'grade_point' => 0.00,
                'remarks' => 'Fail',
                'is_fail' => true,
            ],
        ];
        
        foreach ($gradeScales as $scale) {
            GradeScale::create(array_merge($scale, [
                'grade_system_id' => $gradeSystem->id,
            ]));
        }
    }
} 