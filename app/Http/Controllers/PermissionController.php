<?php

namespace App\Http\Controllers;

use App\Models\Permission; // Import the Permission model
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all(); // Fetch all permissions
        return view('permissions.index', compact('permissions')); // Return the view with permissions
    }

    public function create()
    {
        return view('permissions.create'); // Return the view for creating a new permission
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions|max:255',
        ]);

        Permission::create($request->all()); // Save the new permission
        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.'); // Redirect with success message
    }

    public function edit($id)
    {
        $permission = Permission::findOrFail($id); // Find the permission by ID
        return view('permissions.edit', compact('permission')); // Return the view for editing the permission
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id . '|max:255',
        ]);

        $permission = Permission::findOrFail($id); // Find the permission by ID
        $permission->update($request->all()); // Update the permission
        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.'); // Redirect with success message
    }

    public function destroy($id)
    {
        $permission = Permission::findOrFail($id); // Find the permission by ID
        $permission->delete(); // Delete the permission
        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.'); // Redirect with success message
    }
}