<?php

namespace Database\Seeders;

use App\Models\FeeStructure;
use Illuminate\Database\Seeder;

class FeeStructureSeeder extends Seeder
{
    public function run()
    {
        FeeStructure::create([
            'course_id' => 1,
            'semester' => 1,
            'tuition_fee' => 50000,
            'development_fee' => 5000,
            'other_charges' => 2000,
            'total_amount' => 57000,
            'description' => 'First semester fee structure'
        ]);
    }
}