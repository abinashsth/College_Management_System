<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view roles', ['only' => ['index']]);
        $this->middleware('permission:create roles', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit roles', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete roles', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            $permissions = Permission::whereIn('name', $request->permissions)->get();
            
            $role->syncPermissions($permissions);

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error creating role: ' . $e->getMessage());
        }
    }

    public function edit(Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403);
        }
        
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403);
        }

        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $role->update(['name' => $request->name]);
            
            $permissions = Permission::whereIn('name', $request->permissions)->get();
            
            $role->syncPermissions($permissions);

            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating role: ' . $e->getMessage());
        }
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403);
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }

    public function show(Role $role)
    {
        if ($role->name === 'super-admin') {
            abort(403);
        }
        
        return view('roles.show', compact('role'));
    }
}
