<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Support\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index');
    }

    public function data(Request $request): JsonResponse
    {
        $result = DataTables::make(
            User::query(),
            $request,
            [
                'id' => 'id',
                'name' => 'name',
                'email' => 'email',
                'role_label' => 'role',
            ],
            each: fn (array $row) => [
                ...$row,
                'role_label' => UserRole::from($row['role'])->label(),
                'aksi' => view('admin.users.row-actions', $row)->render(),
            ],
        );

        return response()->json($result);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => null,
            'roles' => UserRole::cases(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->validated();

        if (blank($attributes['password'] ?? null)) {
            unset($attributes['password']);
        }

        $user->update($attributes);

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return to_route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
