<?php

/**
 * This is a standalone script to sync academic structures with traditional tables.
 * It can be run directly from the command line: php sync-academic-structures.php
 */

// Define the __DIR__ constant if it's not already defined (to support inclusion from controller)
if (!defined('__DIR__')) {
    define('__DIR__', dirname(__FILE__));
}

// Initialize a variable to track if we're running in standalone mode
$isStandalone = false;
if (!defined('LARAVEL_START')) {
    $isStandalone = true;
    
    // Bootstrap Laravel when running as standalone
    require __DIR__ . '/vendor/autoload.php';
    
    try {
        // Use include instead of require_once for better error handling
        $bootstrap = include __DIR__ . '/bootstrap/app.php';
        
        // If bootstrap.php returns false, something went wrong
        if (!$bootstrap || !is_object($bootstrap)) {
            echo "Error: Failed to initialize Laravel application\n";
            exit(1);
        }
        
        $kernel = $bootstrap->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
    } catch (\Exception $e) {
        echo "Error bootstrapping Laravel: " . $e->getMessage() . "\n";
        exit(1);
    }
}

// Now use the Laravel application
use App\Models\AcademicStructure;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// Output message only in standalone mode to avoid messing up controller output
if ($isStandalone) {
    echo "Starting synchronization of academic structures...\n";
}

// Check if we're being called from within an existing transaction
$inExistingTransaction = DB::transactionLevel() > 0;

// Only start a transaction if we're not already in one
if (!$inExistingTransaction) {
    DB::beginTransaction();
}

try {
    // First, check if traditional tables have the required columns
    $missingColumns = [];
    
    // Check faculties table
    if (Schema::hasTable('faculties')) {
        if (!Schema::hasColumn('faculties', 'slug')) {
            $missingColumns[] = 'faculties.slug';
            echo "- Adding missing column: faculties.slug\n";
            Schema::table('faculties', function ($table) {
                $table->string('slug')->nullable()->after('name');
            });
        }
        if (!Schema::hasColumn('faculties', 'status')) {
            $missingColumns[] = 'faculties.status';
            echo "- Adding missing column: faculties.status\n";
            Schema::table('faculties', function ($table) {
                $table->boolean('status')->default(true)->after('description');
            });
        }
        if (!Schema::hasColumn('faculties', 'academic_structure_id')) {
            $missingColumns[] = 'faculties.academic_structure_id';
            echo "- Adding missing column: faculties.academic_structure_id\n";
            Schema::table('faculties', function ($table) {
                $table->unsignedBigInteger('academic_structure_id')->nullable()->after('id');
            });
        }
    } else {
        echo "Error: faculties table does not exist!\n";
        exit(1);
    }
    
    // Check departments table
    if (Schema::hasTable('departments')) {
        if (!Schema::hasColumn('departments', 'slug')) {
            $missingColumns[] = 'departments.slug';
            echo "- Adding missing column: departments.slug\n";
            Schema::table('departments', function ($table) {
                $table->string('slug')->nullable()->after('name');
            });
        }
        if (!Schema::hasColumn('departments', 'faculty_id')) {
            $missingColumns[] = 'departments.faculty_id';
            echo "- Adding missing column: departments.faculty_id\n";
            Schema::table('departments', function ($table) {
                $table->unsignedBigInteger('faculty_id')->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('departments', 'status')) {
            $missingColumns[] = 'departments.status';
            echo "- Adding missing column: departments.status\n";
            Schema::table('departments', function ($table) {
                $table->boolean('status')->default(true)->after('description');
            });
        }
        if (!Schema::hasColumn('departments', 'academic_structure_id')) {
            $missingColumns[] = 'departments.academic_structure_id';
            echo "- Adding missing column: departments.academic_structure_id\n";
            Schema::table('departments', function ($table) {
                $table->unsignedBigInteger('academic_structure_id')->nullable()->after('id');
            });
        }
    } else {
        echo "Error: departments table does not exist!\n";
        exit(1);
    }
    
    // Check programs table
    if (Schema::hasTable('programs')) {
        if (!Schema::hasColumn('programs', 'slug')) {
            $missingColumns[] = 'programs.slug';
            echo "- Adding missing column: programs.slug\n";
            Schema::table('programs', function ($table) {
                $table->string('slug')->nullable()->after('name');
            });
        }
        if (!Schema::hasColumn('programs', 'department_id')) {
            $missingColumns[] = 'programs.department_id';
            echo "- Adding missing column: programs.department_id\n";
            Schema::table('programs', function ($table) {
                $table->unsignedBigInteger('department_id')->nullable()->after('description');
            });
        }
        if (!Schema::hasColumn('programs', 'status')) {
            $missingColumns[] = 'programs.status';
            echo "- Adding missing column: programs.status\n";
            Schema::table('programs', function ($table) {
                $table->boolean('status')->default(true)->after('description');
            });
        }
        if (!Schema::hasColumn('programs', 'duration')) {
            $missingColumns[] = 'programs.duration';
            echo "- Adding missing column: programs.duration\n";
            Schema::table('programs', function ($table) {
                $table->integer('duration')->default(4)->after('department_id');
            });
        }
        if (!Schema::hasColumn('programs', 'duration_unit')) {
            $missingColumns[] = 'programs.duration_unit';
            echo "- Adding missing column: programs.duration_unit\n";
            Schema::table('programs', function ($table) {
                $table->string('duration_unit')->default('years')->after('duration');
            });
        }
        if (!Schema::hasColumn('programs', 'credit_hours')) {
            $missingColumns[] = 'programs.credit_hours';
            echo "- Adding missing column: programs.credit_hours\n";
            Schema::table('programs', function ($table) {
                $table->integer('credit_hours')->default(120)->after('duration_unit');
            });
        }
        if (!Schema::hasColumn('programs', 'degree_level')) {
            $missingColumns[] = 'programs.degree_level';
            echo "- Adding missing column: programs.degree_level\n";
            Schema::table('programs', function ($table) {
                $table->string('degree_level')->default('Bachelor')->after('credit_hours');
            });
        }
        if (!Schema::hasColumn('programs', 'academic_structure_id')) {
            $missingColumns[] = 'programs.academic_structure_id';
            echo "- Adding missing column: programs.academic_structure_id\n";
            Schema::table('programs', function ($table) {
                $table->unsignedBigInteger('academic_structure_id')->nullable()->after('id');
            });
        }
    } else {
        echo "Error: programs table does not exist!\n";
        exit(1);
    }
    
    if (count($missingColumns) > 0) {
        echo "Added " . count($missingColumns) . " missing columns to the database schema.\n";
    } else {
        echo "Database schema is up to date, no columns needed to be added.\n";
    }
    
    // Now sync academic structures to the traditional tables
    
    // Start with faculties (top level)
    $faculties = AcademicStructure::where('type', 'faculty')->get();
    $facultyCount = 0;
    
    foreach ($faculties as $faculty) {
        echo "Processing faculty: {$faculty->name}\n";
        
        // Check if a faculty with this code already exists
        $existingFaculty = Faculty::where('code', $faculty->code)->first();
        
        if ($existingFaculty) {
            echo "- Faculty already exists with code {$faculty->code}, updating...\n";
            $updateData = [
                'name' => $faculty->name,
                'description' => $faculty->description,
            ];
            
            // Only add fields if they exist in the table
            if (Schema::hasColumn('faculties', 'slug')) {
                $updateData['slug'] = Str::slug($faculty->name);
            }
            if (Schema::hasColumn('faculties', 'status')) {
                $updateData['status'] = $faculty->is_active;
            }
            if (Schema::hasColumn('faculties', 'academic_structure_id')) {
                $updateData['academic_structure_id'] = $faculty->id;
            }
            
            $existingFaculty->update($updateData);
        } else {
            echo "- Creating new faculty with code {$faculty->code}...\n";
            
            // First get the table structure for the faculties table directly from the database
            $columns = DB::select("SHOW COLUMNS FROM faculties");
            $columnNames = array_map(function($col) { return $col->Field; }, $columns);
            
            // Prepare field arrays based on what columns actually exist
            $fields = [];
            $values = [];
            $placeholders = [];
            
            // Always include these basic fields
            $fields[] = 'name';
            $values[] = $faculty->name;
            $placeholders[] = '?';
            
            $fields[] = 'code';
            $values[] = $faculty->code;
            $placeholders[] = '?';
            
            $fields[] = 'description';
            $values[] = $faculty->description;
            $placeholders[] = '?';
            
            // Check for optional fields
            if (in_array('slug', $columnNames)) {
                $fields[] = 'slug';
                $values[] = Str::slug($faculty->name);
                $placeholders[] = '?';
            }
            
            if (in_array('status', $columnNames)) {
                $fields[] = 'status';
                $values[] = $faculty->is_active;
                $placeholders[] = '?';
            }
            
            // Add academic_structure_id if it exists
            if (in_array('academic_structure_id', $columnNames)) {
                $fields[] = 'academic_structure_id';
                $values[] = $faculty->id;
                $placeholders[] = '?';
            }
            
            // Add created_at and updated_at fields
            $now = now();
            if (in_array('created_at', $columnNames)) {
                $fields[] = 'created_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            if (in_array('updated_at', $columnNames)) {
                $fields[] = 'updated_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            // Build and execute the SQL query directly
            $fieldString = implode(', ', $fields);
            $placeholderString = implode(', ', $placeholders);
            
            $sql = "INSERT INTO faculties ($fieldString) VALUES ($placeholderString)";
            DB::insert($sql, $values);
            
            echo "- Faculty created using direct SQL query\n";
        }
        
        $facultyCount++;
    }
    
    echo "Synchronized $facultyCount faculties.\n";
    
    // Then departments (second level)
    $departments = AcademicStructure::where('type', 'department')->get();
    $departmentCount = 0;
    
    foreach ($departments as $department) {
        echo "Processing department: {$department->name}\n";
        
        // A department must have a parent faculty
        if (!$department->parent_id) {
            echo "- Department {$department->name} has no parent, skipping...\n";
            continue;
        }
        
        $parentStructure = AcademicStructure::find($department->parent_id);
        
        if (!$parentStructure || $parentStructure->type !== 'faculty') {
            echo "- Department {$department->name} doesn't have a faculty parent, skipping...\n";
            continue;
        }
        
        // Find the corresponding faculty in the traditional system
        $faculty = Faculty::where('code', $parentStructure->code)->first();
        
        if (!$faculty) {
            echo "- Parent faculty not found for department {$department->name}, skipping...\n";
            continue;
        }
        
        // Check if a department with this code already exists
        $existingDepartment = Department::where('code', $department->code)->first();
        
        if ($existingDepartment) {
            echo "- Department already exists with code {$department->code}, updating...\n";
            $updateData = [
                'name' => $department->name,
                'description' => $department->description,
                'faculty_id' => $faculty->id,
            ];
            
            // Only add fields if they exist in the table
            if (Schema::hasColumn('departments', 'slug')) {
                $updateData['slug'] = Str::slug($department->name);
            }
            if (Schema::hasColumn('departments', 'status')) {
                $updateData['status'] = $department->is_active;
            }
            if (Schema::hasColumn('departments', 'academic_structure_id')) {
                $updateData['academic_structure_id'] = $department->id;
            }
            
            $existingDepartment->update($updateData);
        } else {
            echo "- Creating new department with code {$department->code}...\n";
            
            // First get the table structure for the departments table directly from the database
            $columns = DB::select("SHOW COLUMNS FROM departments");
            $columnNames = array_map(function($col) { return $col->Field; }, $columns);
            
            // Prepare field arrays based on what columns actually exist
            $fields = [];
            $values = [];
            $placeholders = [];
            
            // Always include these basic fields
            $fields[] = 'name';
            $values[] = $department->name;
            $placeholders[] = '?';
            
            $fields[] = 'code';
            $values[] = $department->code;
            $placeholders[] = '?';
            
            $fields[] = 'description';
            $values[] = $department->description;
            $placeholders[] = '?';
            
            $fields[] = 'faculty_id';
            $values[] = $faculty->id;
            $placeholders[] = '?';
            
            // Check for optional fields
            if (in_array('slug', $columnNames)) {
                $fields[] = 'slug';
                $values[] = Str::slug($department->name);
                $placeholders[] = '?';
            }
            
            if (in_array('status', $columnNames)) {
                $fields[] = 'status';
                $values[] = $department->is_active;
                $placeholders[] = '?';
            }
            
            // Add academic_structure_id if it's required (assuming it's required if it exists)
            if (in_array('academic_structure_id', $columnNames)) {
                $fields[] = 'academic_structure_id';
                $values[] = $department->id;
                $placeholders[] = '?';
            }
            
            // Add created_at and updated_at fields
            $now = now();
            if (in_array('created_at', $columnNames)) {
                $fields[] = 'created_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            if (in_array('updated_at', $columnNames)) {
                $fields[] = 'updated_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            // Build and execute the SQL query directly
            $fieldString = implode(', ', $fields);
            $placeholderString = implode(', ', $placeholders);
            
            $sql = "INSERT INTO departments ($fieldString) VALUES ($placeholderString)";
            DB::insert($sql, $values);
            
            echo "- Department created using direct SQL query\n";
        }
        
        $departmentCount++;
    }
    
    echo "Synchronized $departmentCount departments.\n";
    
    // Finally programs (third level)
    $programs = AcademicStructure::where('type', 'program')->get();
    $programCount = 0;
    
    foreach ($programs as $program) {
        echo "Processing program: {$program->name}\n";
        
        // A program must have a parent department
        if (!$program->parent_id) {
            echo "- Program {$program->name} has no parent, skipping...\n";
            continue;
        }
        
        $parentStructure = AcademicStructure::find($program->parent_id);
        
        if (!$parentStructure || $parentStructure->type !== 'department') {
            echo "- Program {$program->name} doesn't have a department parent, skipping...\n";
            continue;
        }
        
        // Find the corresponding department in the traditional system
        $department = Department::where('code', $parentStructure->code)->first();
        
        if (!$department) {
            echo "- Parent department not found for program {$program->name}, skipping...\n";
            continue;
        }
        
        // Check if a program with this code already exists
        $existingProgram = Program::where('code', $program->code)->first();
        
        if ($existingProgram) {
            echo "- Program already exists with code {$program->code}, updating...\n";
            $updateData = [
                'name' => $program->name,
                'description' => $program->description,
                'department_id' => $department->id,
            ];
            
            // Only add fields if they exist in the table
            if (Schema::hasColumn('programs', 'slug')) {
                $updateData['slug'] = Str::slug($program->name);
            }
            if (Schema::hasColumn('programs', 'status')) {
                $updateData['status'] = $program->is_active;
            }
            if (Schema::hasColumn('programs', 'academic_structure_id')) {
                $updateData['academic_structure_id'] = $program->id;
            }
            
            $existingProgram->update($updateData);
        } else {
            echo "- Creating new program with code {$program->code}...\n";
            
            // First get the table structure for the programs table directly from the database
            $columns = DB::select("SHOW COLUMNS FROM programs");
            $columnNames = array_map(function($col) { return $col->Field; }, $columns);
            
            // Prepare field arrays based on what columns actually exist
            $fields = [];
            $values = [];
            $placeholders = [];
            
            // Always include these basic fields
            $fields[] = 'name';
            $values[] = $program->name;
            $placeholders[] = '?';
            
            $fields[] = 'code';
            $values[] = $program->code;
            $placeholders[] = '?';
            
            $fields[] = 'description';
            $values[] = $program->description;
            $placeholders[] = '?';
            
            $fields[] = 'department_id';
            $values[] = $department->id;
            $placeholders[] = '?';
            
            // Check for optional fields
            if (in_array('slug', $columnNames)) {
                $fields[] = 'slug';
                $values[] = Str::slug($program->name);
                $placeholders[] = '?';
            }
            
            if (in_array('status', $columnNames)) {
                $fields[] = 'status';
                $values[] = $program->is_active;
                $placeholders[] = '?';
            }
            
            // Add academic_structure_id if it exists
            if (in_array('academic_structure_id', $columnNames)) {
                $fields[] = 'academic_structure_id';
                $values[] = $program->id;
                $placeholders[] = '?';
            }
            
            // Add additional fields
            if (in_array('duration', $columnNames)) {
                $fields[] = 'duration';
                $values[] = 4; // Default value
                $placeholders[] = '?';
            }
            
            if (in_array('duration_unit', $columnNames)) {
                $fields[] = 'duration_unit';
                $values[] = 'years'; // Default value
                $placeholders[] = '?';
            }
            
            if (in_array('credit_hours', $columnNames)) {
                $fields[] = 'credit_hours';
                $values[] = 120; // Default value
                $placeholders[] = '?';
            }
            
            if (in_array('degree_level', $columnNames)) {
                $fields[] = 'degree_level';
                $values[] = 'Bachelor'; // Default value
                $placeholders[] = '?';
            }
            
            // Add created_at and updated_at fields
            $now = now();
            if (in_array('created_at', $columnNames)) {
                $fields[] = 'created_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            if (in_array('updated_at', $columnNames)) {
                $fields[] = 'updated_at';
                $values[] = $now;
                $placeholders[] = '?';
            }
            
            // Build and execute the SQL query directly
            $fieldString = implode(', ', $fields);
            $placeholderString = implode(', ', $placeholders);
            
            $sql = "INSERT INTO programs ($fieldString) VALUES ($placeholderString)";
            DB::insert($sql, $values);
            
            echo "- Program created using direct SQL query\n";
        }
        
        $programCount++;
    }
    
    echo "Synchronized $programCount programs.\n";
    
    // Commit the transaction only if we started it
    if (!$inExistingTransaction) {
        DB::commit();
    }
    
    if ($isStandalone) {
        echo "Synchronization completed successfully.\n";
    }
    
} catch (\Exception $e) {
    // Rollback the transaction only if we started it
    if (!$inExistingTransaction) {
        DB::rollBack();
    }
    
    if ($isStandalone) {
        echo "Error during synchronization: " . $e->getMessage() . "\n";
        exit(1);
    } else {
        // When called from controller, re-throw the exception to be handled by the controller
        throw $e;
    }
} 