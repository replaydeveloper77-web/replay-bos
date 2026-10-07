<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Customer;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $this->authorize('create', User::class);

        $roles = Role::orderBy('name')->get();
        $resellers = Customer::where('category', 'RESELLER')->where('is_active', true)->orderBy('name')->get();

        return view('users.create', compact('roles', 'resellers'));
    }

    public function store(StoreUserRequest $request)
    {
        $this->userService->create($request->validated(), $request->user(), $request);

        return redirect()->route('users.index')->with('status', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::orderBy('name')->get();
        $resellers = Customer::where('category', 'RESELLER')->where('is_active', true)->orderBy('name')->get();
        $user->load('roles');

        return view('users.edit', compact('user', 'roles', 'resellers'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated(), $request->user(), $request);

        return redirect()->route('users.index')->with('status', 'User berhasil diperbarui.');
    }

    public function disable(Request $request, User $user)
    {
        $this->authorize('disable', $user);

        $this->userService->setActive($user, false, $request->user(), $request);

        return redirect()->route('users.index')->with('status', 'User berhasil dinonaktifkan.');
    }

    public function enable(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $this->userService->setActive($user, true, $request->user(), $request);

        return redirect()->route('users.index')->with('status', 'User berhasil diaktifkan.');
    }
}
