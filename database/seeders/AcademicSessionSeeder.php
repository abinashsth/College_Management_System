<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    public function run()
    {
        AcademicSession::updateOrCreate(
            ['name' => '2024-2025'],
            [
                'start_date' => '2024-04-01',
                'end_date' => '2025-03-31',
                'is_active' => true,
                'description' => 'Academic Year 2024-2025'
            ]
        );

        // Add another session for testing
        AcademicSession::updateOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_active' => false,
                'description' => 'Academic Year 2025-2026'
            ]
        );
    }
}
