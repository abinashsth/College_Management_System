<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add the verify marks permission
$permission = \Spatie\Permission\Models\Permission::firstOrCreate([
    'name' => 'verify marks',
    'guard_name' => 'web'
]);

// Assign to roles
$adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
if ($adminRole) {
    $adminRole->givePermissionTo('verify marks');
    echo "Permission 'verify marks' assigned to admin role\n";
}

$superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
if ($superAdminRole) {
    $superAdminRole->givePermissionTo('verify marks');
    echo "Permission 'verify marks' assigned to super-admin role\n";
}

// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

echo "Permission 'verify marks' added successfully!\n"; 