<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditService;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with('role');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('id', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15);
        $roles = \App\Models\Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,suspended',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Assign role relation is handled by role_id in create if fillable, 
        // OR we need to use assignRole if role_id is not fillable.
        // User model fillable didn't have role_id? Let's check.
        // If not in fillable, we can manually assign. But create accepts array.
        // Assuming role_id is added to fillable or guarded is empty. 
        // Best practice: update fillable or force fill.
        // Actually, User.php fillable was: 'name', 'email', 'password', 'status', 'last_login_at'.
        // So role_id is NOT fillable. I should add it or use assignRole.

        $role = \App\Models\Role::find($validated['role_id']);
        $user->assignRole($role);
        // Note: User::create doesn't save role_id if not fillable. 
        // But assignRole() does save(). So it's fine.
        // But status is fillable.

        AuditService::log('create', 'User', $user->id, null, $user->toArray());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = \App\Models\Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,suspended',
        ]);

        $oldValues = $user->toArray();

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->fill($validated);

        // Handle role update
        $role = \App\Models\Role::find($validated['role_id']);
        $user->role()->associate($role);

        $user->save();

        AuditService::log('update', 'User', $user->id, $oldValues, $user->fresh()->toArray());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $oldValues = $user->toArray();

        // Soft delete by suspending
        $user->update(['status' => 'suspended']);

        AuditService::log('delete', 'User', $user->id, $oldValues, ['status' => 'suspended']);

        return redirect()->route('admin.users.index')->with('success', 'User suspended successfully.');
    }
}
