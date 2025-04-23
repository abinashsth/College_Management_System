<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

// Check if the column already exists
if (!Schema::hasColumn('academic_sessions', 'is_active')) {
    echo "Adding 'is_active' column to academic_sessions table...\n";
    
    // Add the column
    Schema::table('academic_sessions', function (Blueprint $table) {
        $table->boolean('is_active')->default(false)->after('is_current');
    });
    
    // Set is_active to true for records where is_current is true
    DB::statement('UPDATE academic_sessions SET is_active = is_current');
    
    echo "Column 'is_active' added successfully!\n";
} else {
    echo "Column 'is_active' already exists in academic_sessions table.\n";
}

// Set current session as active if no active session exists
$activeSessionExists = DB::table('academic_sessions')->where('is_active', 1)->exists();
if (!$activeSessionExists) {
    // Get the most recent academic session and set it as active
    $latestSession = DB::table('academic_sessions')->latest('start_date')->first();
    if ($latestSession) {
        DB::table('academic_sessions')->where('id', $latestSession->id)->update(['is_active' => true]);
        echo "Set most recent session (ID: {$latestSession->id}) as active.\n";
    } else {
        echo "No academic sessions found to set as active.\n";
    }
} 