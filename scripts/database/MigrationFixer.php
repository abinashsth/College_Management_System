<?php

namespace Scripts\Database;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrationFixer
{
    protected $migrationsPath;
    protected $duplicateMigrations = [
        'create_marks_table' => [
            'keep' => '2025_04_10_000001_create_marks_table.php',
            'remove' => ['2024_08_17_000001_create_marks_table.php']
        ],
        'create_subject_masks_table' => [
            'keep' => '2024_09_18_000001_create_subject_masks_table.php',
            'remove' => [
                '2023_06_01_000000_create_masks_table.php',
                '2023_07_01_000000_create_masks_table.php',
                '2023_10_20_000000_create_masks_table.php',
                '2024_09_17_000001_create_subject_masks_table.php'
            ]
        ],
        'create_class_subjects_table' => [
            'keep' => '2025_04_13_171419_create_class_subjects_table.php',
            'remove' => [
                '2025_04_13_171353_create_class_subjects_table.php',
                '2025_04_13_000001_create_class_subjects_table.php'
            ]
        ],
        'create_exams_table' => [
            'keep' => '2025_04_09_083237_create_exams_table.php',
            'remove' => ['2024_08_16_000003_create_exams_table.php']
        ]
    ];

    public function __construct()
    {
        $this->migrationsPath = database_path('migrations');
    }

    public function fixDuplicateMigrations()
    {
        foreach ($this->duplicateMigrations as $migration => $files) {
            echo "Fixing {$migration}...\n";
            
            // Ensure we keep the specified file
            if (!File::exists($this->migrationsPath . '/' . $files['keep'])) {
                echo "Warning: Keep file {$files['keep']} not found!\n";
                continue;
            }

            // Remove duplicate files
            foreach ($files['remove'] as $file) {
                $path = $this->migrationsPath . '/' . $file;
                if (File::exists($path)) {
                    File::delete($path);
                    echo "Removed duplicate file: {$file}\n";
                }
            }
        }
    }

    public function addMissingForeignKeys()
    {
        if (!Schema::hasTable('migrations')) {
            echo "Migrations table not found. Please run migrations first.\n";
            return;
        }

        // Create the fix_foreign_key_constraints migration if it doesn't exist
        $fixMigrationPath = $this->migrationsPath . '/2025_04_16_000001_fix_foreign_key_constraints.php';
        if (!File::exists($fixMigrationPath)) {
            File::put($fixMigrationPath, $this->getForeignKeyFixMigration());
            echo "Created foreign key fix migration.\n";
        }
    }

    public function syncPermissions()
    {
        if (!Schema::hasTable('permissions')) {
            echo "Permissions table not found. Please run migrations first.\n";
            return;
        }

        $permissions = [
            'view_marks',
            'add_marks',
            'edit_marks',
            'delete_marks',
            'verify_marks',
            'publish_marks'
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission],
                [
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }

        echo "Permissions synced successfully.\n";
    }

    protected function getForeignKeyFixMigration()
    {
        return File::get(__DIR__ . '/templates/fix_foreign_key_constraints.php');
    }
}

// Create an instance and run the fixes if this script is executed directly
if (php_sapi_name() === 'cli') {
    require_once __DIR__ . '/../../vendor/autoload.php';

    $fixer = new MigrationFixer();
    
    echo "Starting migration fixes...\n";
    $fixer->fixDuplicateMigrations();
    $fixer->addMissingForeignKeys();
    $fixer->syncPermissions();
    echo "Migration fixes completed.\n";
} 