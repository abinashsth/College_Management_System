<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view users', ['only' => ['index', 'show']]);
        $this->middleware('permission:create users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete users', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = User::with('roles');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $role = $request->input('role');
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->latest()->paginate(10);
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->get();
        \Log::info('Available roles: ' . $roles->pluck('name'));
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        \DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign multiple roles
            $roles = Role::whereIn('name', $request->roles)->get();
            $user->syncRoles($roles);

            \DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User created successfully with roles: ' . $roles->pluck('name')->implode(', '));
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating user: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error creating user: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        \DB::beginTransaction();

        try {
            // Update basic info
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Sync roles
            $roles = Role::whereIn('name', $request->roles)->get();
            $user->syncRoles($roles);

            \DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully with roles: ' . $roles->pluck('name')->implode(', '));
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error updating user: ' . $e->getMessage());
            return back()->withInput()
                ->with('error', 'Error updating user: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete super-admin user');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function showChangePasswordForm()
    {
        return view('users.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'password' => 'required|string|min:8|confirmed|different:current_password',
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}