# Migration Scripts

This directory contains utility scripts for the College Management System.

## migration_fix.php

A script to fix naming inconsistencies in migration files.

### Features

1. **Corrects future dated timestamps** - Changes any migration files with future dates (e.g., 2025) to the current year.
2. **Standardizes migration file naming** - Ensures all migration files follow the standard Laravel format: `YYYY_MM_DD_HHMMSS_migration_name.php`
3. **Identifies and moves duplicate migrations** - Identifies duplicate migrations and keeps only the most recent version
4. **Creates an SQL update script** - Generates an SQL script to update the migrations table in the database

### Usage

1. Make sure you have a backup of your database and migrations directory.
2. Update the `$dryRun` variable at the top of the script if you want to do a trial run:
   - `$dryRun = true`: Only simulates the changes without modifying any files
   - `$dryRun = false`: Actually applies the changes

3. Run the script from the project root:
   ```
   php migration_fix.php
   ```

4. The script will:
   - Create a backup of all migration files in `database/migrations_backup_YYYYMMDD_HHMMSS/`
   - Rename files with future dates to the current year
   - Standardize naming of files with non-standard formats
   - Move duplicate migrations to a `duplicates` folder in the backup directory
   - Create a log of all changes in `migration_changes.log`
   - Generate an SQL script to update the migrations table

5. After running the script, review the changes in the generated log file.

6. If needed, run the generated SQL script against your database to update the migrations table:
   ```
   mysql -u username -p database_name < database/migrations_backup_YYYYMMDD_HHMMSS/fix_migrations_table.sql
   ```

### Notes

- The script will never delete any files, only move or rename them.
- All original files are backed up before any changes are made.
- If you encounter any issues with the renamed migrations, you can restore from the backup directory. 