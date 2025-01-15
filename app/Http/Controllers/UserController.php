<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // Fetch users with roles, paginated (10 users per page)
        $users = User::with('roles')->paginate(10);
        
        return view('users.index', compact('users'));
    }
    
    public function edit($id)
    {
        $user = User::findOrFail($id); // Fetch the user by ID
        $roles = Role::orderBy('name', 'ASC')->get();

        return view('users.edit', [
            'user' => $user,
            'roles' => $roles
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update roles
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles); // Sync roles
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($request->has('roles')) {
            $user->roles()->attach($request->roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    // New method to show the profile
    public function profile()
    {
        $user = auth()->user(); // Get the authenticated user
        $roles = $user->roles; // Get the roles associated with the user
        $permissions = $user->permissions; // Get the permissions associated with the user
        return view('users.profile', compact('user', 'roles', 'permissions'));
    }

    // New method to update the profile
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:6|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        // Update roles only if roles are provided
        if ($request->has('roles')) {
            $user->roles()->sync($request->roles); // Sync roles
        }

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }
}
