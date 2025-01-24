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

    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['super-admin'])->get();
        \Log::info('Available roles for user creation:', [
            'count' => $roles->count(),
            'roles' => $roles->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            })
        ]);
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        \Log::info('Creating new user with data:', $request->all());

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        \Log::info('Validation passed');
        \DB::beginTransaction();

        try {
            \Log::info('Creating user record');
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            \Log::info('User record created:', ['user_id' => $user->id]);

            if ($request->hasFile('profile_photo')) {
                \Log::info('Processing profile photo');
                $user->updateProfilePhoto($request->file('profile_photo'));
            }

            \Log::info('Assigning roles:', ['roles' => $request->roles]);
            $roles = Role::whereIn('id', $request->roles)->get();
            \Log::info('Found roles:', ['role_count' => $roles->count(), 'roles' => $roles->pluck('name')]);
            $user->syncRoles($roles);

            \DB::commit();
            \Log::info('User created successfully');

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error creating user: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
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
            'roles.*' => 'exists:roles,id',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:1024'
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

            // Update profile photo if provided
            if ($request->hasFile('profile_photo')) {
                $user->updateProfilePhoto($request->file('profile_photo'));
            }

            // Sync roles using IDs
            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);

            \DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');
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