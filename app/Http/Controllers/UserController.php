<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\SecurityService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->except(['show', 'edit', 'update']);
        $this->securityService = $securityService;
    }

    public function index()
    {
        $users = User::with('role')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => User::passwordRules(),
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->role()->associate(Role::find($validated['role_id']));
        $user->save();

        $this->securityService->logSecurityEvent(
            auth()->id(),
            'user_created',
            $request->ip(),
            $request->userAgent(),
            true,
            ['user_id' => $user->id]
        );

        return redirect()->route('users.show', $user)->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $rules = [
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ];

        if ($request->filled('password')) {
            $rules['password'] = User::passwordRules();
        }

        if (auth()->user()->isAdmin()) {
            $rules['role_id'] = 'required|exists:roles,id';
            $rules['status'] = 'required|in:active,suspended,closed';
        }

        $validated = $request->validate($rules);

        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];

        if (isset($validated['password'])) {
            $user->password = $validated['password'];
        }

        if (auth()->user()->isAdmin()) {
            $user->status = $validated['status'];
            $user->role()->associate(Role::find($validated['role_id']));
        }

        $user->save();

        $this->securityService->logSecurityEvent(
            auth()->id(),
            'user_updated',
            $request->ip(),
            $request->userAgent(),
            true,
            ['user_id' => $user->id]
        );

        return redirect()->route('users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        $this->securityService->logSecurityEvent(
            auth()->id(),
            'user_deleted',
            request()->ip(),
            request()->userAgent(),
            true,
            ['user_id' => $user->id]
        );

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
