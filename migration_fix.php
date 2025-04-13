<?php

/**
 * Migration Fix Script
 * 
 * Fixes naming inconsistencies in migration files:
 * 1. Corrects future dated timestamps (2025 to 2024)
 * 2. Standardizes migration file naming
 * 3. Identifies and marks duplicate migrations
 */

// Get the database directory
$migrationsDir = __DIR__ . '/database/migrations';
$backupDir = __DIR__ . '/database/migrations_backup_' . date('Ymd_His');
$logFile = __DIR__ . '/migration_fix_' . date('Ymd_His') . '.log';
$currentYear = date('Y');
$dryRun = true; // Set to true to preview changes without applying them

// Start capturing output
ob_start();

// Make sure the migrations directory exists
if (!is_dir($migrationsDir)) {
    die("Error: Migrations directory not found at $migrationsDir\n");
}

// Create backup directory
if (!$dryRun && !is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "Created backup directory: $backupDir\n";
}

// Get all migration files
$migrations = glob($migrationsDir . '/*.php');

// Backup all files first
if (!$dryRun) {
    echo "Backing up all migration files...\n";
    foreach ($migrations as $migration) {
        $filename = basename($migration);
        copy($migration, $backupDir . '/' . $filename);
    }
    echo "Backup complete.\n\n";
}

// Migration change log
$changes = [
    'renamed_files' => [],
    'marked_duplicates' => [],
];

// Find problematic migrations
echo "Finding problematic migrations...\n";

// 1. Fix future dates in migrations (2025_* to 2024_*)
$futureDatedFiles = [];
foreach ($migrations as $migration) {
    $filename = basename($migration);
    
    // Check for future year dates
    if (preg_match('/^(20[2-9][5-9])_(\d{2})_(\d{2})/', $filename, $matches)) {
        $futureDatedFiles[$filename] = $migration;
    }
}

echo "Found " . count($futureDatedFiles) . " files with future dates.\n";

// 2. Fix migration files with non-standard naming
$nonStandardFiles = [];
foreach ($migrations as $migration) {
    $filename = basename($migration);
    
    // Check for non-standard migration names (missing timestamp parts)
    if (!preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_/', $filename) && 
        preg_match('/^\d{4}_\d{2}_\d{2}/', $filename)) {
        $nonStandardFiles[$filename] = $migration;
    } else if (!preg_match('/^\d{4}_\d{2}_\d{2}/', $filename) && 
               !preg_match('/^0001_01_01/', $filename)) {
        // These are migrations with completely invalid names (missing timestamp)
        $nonStandardFiles[$filename] = $migration;
    }
}

echo "Found " . count($nonStandardFiles) . " files with non-standard naming.\n";

// 3. Find duplicate migrations
$baseNameMap = [];
foreach ($migrations as $migration) {
    $filename = basename($migration);
    
    // Extract base name by removing timestamp
    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $filename, $matches)) {
        $baseName = $matches[1];
    } else if (preg_match('/^\d{4}_\d{2}_\d{2}_(.+)$/', $filename, $matches)) {
        $baseName = $matches[1];
    } else if (preg_match('/^(\d{4}_\d{2}_\d{2})(.+)$/', $filename, $matches)) {
        $baseName = $matches[2];
    } else {
        $baseName = $filename;
    }
    
    if (!isset($baseNameMap[$baseName])) {
        $baseNameMap[$baseName] = [];
    }
    
    $baseNameMap[$baseName][] = $filename;
}

$duplicates = [];
foreach ($baseNameMap as $baseName => $files) {
    if (count($files) > 1) {
        $duplicates[$baseName] = $files;
    }
}

echo "Found " . count($duplicates) . " sets of duplicate migrations.\n\n";

// Apply fixes if not in dry run mode
if (!$dryRun) {
    echo "Applying fixes...\n";
    
    // 1. Fix future dates
    echo "Fixing future dates...\n";
    foreach ($futureDatedFiles as $filename => $path) {
        $newFilename = preg_replace('/^20[2-9][5-9]/', $currentYear, $filename);
        $newPath = $migrationsDir . '/' . $newFilename;
        
        if (file_exists($newPath)) {
            echo "   - Can't rename $filename to $newFilename - file already exists\n";
            continue;
        }
        
        echo "   - Renaming $filename to $newFilename\n";
        rename($path, $newPath);
        $changes['renamed_files'][$filename] = $newFilename;
    }
    
    // 2. Fix non-standard naming
    echo "\nFixing non-standard naming...\n";
    foreach ($nonStandardFiles as $filename => $path) {
        // For files with date but no time component
        if (preg_match('/^(\d{4}_\d{2}_\d{2})_(.+)$/', $filename, $matches)) {
            $date = $matches[1];
            $name = $matches[2];
            $time = '000001'; // Add a default time component
            
            $newFilename = "{$date}_{$time}_{$name}";
        } 
        // For files with no timestamp at all
        else if (!preg_match('/^\d{4}_\d{2}_\d{2}/', $filename) && 
                 !preg_match('/^0001_01_01/', $filename)) {
            $date = date('Y_m_d');
            $time = sprintf('%06d', 1);
            $newFilename = "{$date}_{$time}_{$filename}";
        } else {
            // Skip files that don't match criteria
            continue;
        }
        
        $newPath = $migrationsDir . '/' . $newFilename;
        
        if (file_exists($newPath)) {
            echo "   - Can't rename $filename to $newFilename - file already exists\n";
            continue;
        }
        
        echo "   - Renaming $filename to $newFilename\n";
        rename($path, $newPath);
        $changes['renamed_files'][$filename] = $newFilename;
    }
    
    // 3. Mark duplicate migrations (move to a separate directory)
    echo "\nMarking duplicate migrations...\n";
    $duplicateDir = $backupDir . '/duplicates';
    if (!is_dir($duplicateDir)) {
        mkdir($duplicateDir, 0755, true);
    }
    
    foreach ($duplicates as $baseName => $files) {
        // Sort files by timestamp (newest first)
        usort($files, function($a, $b) {
            return strcmp($b, $a);
        });
        
        // Keep the newest file, move others to duplicate directory
        $keepFile = $files[0];
        
        echo "   - Keeping $keepFile\n";
        
        for ($i = 1; $i < count($files); $i++) {
            $moveFile = $files[$i];
            echo "      - Moving duplicate $moveFile to backup/duplicates\n";
            rename($migrationsDir . '/' . $moveFile, $duplicateDir . '/' . $moveFile);
            $changes['marked_duplicates'][$moveFile] = $keepFile;
        }
    }
    
    // Save the change log
    file_put_contents($logFile, json_encode($changes, JSON_PRETTY_PRINT));
    echo "\nChanges have been logged to $logFile\n";
} else {
    echo "DRY RUN - No changes applied. Set \$dryRun = false to apply changes.\n";
}

echo "\nDone.\n";

// Create a SQL script to update the migrations table if needed
if (!$dryRun) {
    $sqlFile = $backupDir . '/fix_migrations_table.sql';
    
    $sql = "-- Run this script to update the migrations table after fixing migration filenames\n\n";
    
    foreach ($changes['renamed_files'] as $oldName => $newName) {
        $oldName = pathinfo($oldName, PATHINFO_FILENAME);
        $newName = pathinfo($newName, PATHINFO_FILENAME);
        
        $sql .= "UPDATE migrations SET migration = '$newName' WHERE migration = '$oldName';\n";
    }
    
    foreach ($changes['marked_duplicates'] as $oldName => $keepName) {
        $oldName = pathinfo($oldName, PATHINFO_FILENAME);
        $sql .= "DELETE FROM migrations WHERE migration = '$oldName';\n";
    }
    
    file_put_contents($sqlFile, $sql);
    echo "SQL script to update migrations table created at $sqlFile\n";
}

// End output capture and write to log file
$output = ob_get_clean();
echo $output; // Display to console
file_put_contents($logFile, $output); // Write to log file
echo "\nLog file created at: $logFile\n"; 