<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add the missing permission
$permission = \Spatie\Permission\Models\Permission::firstOrCreate([
    'name' => 'create marks',
    'guard_name' => 'web'
]);

// Assign to roles
$adminRole = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
if ($adminRole) {
    $adminRole->givePermissionTo('create marks');
    echo "Permission 'create marks' assigned to admin role\n";
}

$teacherRole = \Spatie\Permission\Models\Role::where('name', 'teacher')->first();
if ($teacherRole) {
    $teacherRole->givePermissionTo('create marks');
    echo "Permission 'create marks' assigned to teacher role\n";
}

$superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super-admin')->first();
if ($superAdminRole) {
    $superAdminRole->givePermissionTo('create marks');
    echo "Permission 'create marks' assigned to super-admin role\n";
}

// Clear permission cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

echo "Permission 'create marks' added successfully!\n"; 