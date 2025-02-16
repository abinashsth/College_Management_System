<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYears = [
            [
                'year' => '2024-2025',
                'start_date' => '2024-01-01',
                'end_date' => '2025-12-31',
                'status' => 'active'
            ],
            [
                'year' => '2025-2026', 
                'start_date' => '2025-01-01',
                'end_date' => '2026-12-31',
                'status' => 'active'
            ],
            [
                'year' => '2026-2027',
                'start_date' => '2026-01-01', 
                'end_date' => '2027-12-31',
                'status' => 'active'
            ]
        ];

        foreach ($academicYears as $year) {
            AcademicYear::create($year);
        }
    }
}
