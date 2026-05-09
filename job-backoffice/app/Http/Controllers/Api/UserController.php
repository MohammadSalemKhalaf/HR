<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class UserController extends BaseApiController
{
    /**
     * List all users with pagination, filtering by archived status
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by archived status
        if ($request->archived === 'true') {
            $query->onlyTrashed();
        } else {
            $query->latest();
        }

        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);

        $userData = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roleSlug(),
                'role_name' => $user->roleName(),
                'deleted_at' => $user->deleted_at,
            ];
        })->all();

        return $this->success('Users retrieved successfully', [
            'data' => $userData,
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ]
        ]);
    }

    /**
     * Get a single user by ID
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return $this->success('User retrieved successfully', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->roleSlug(),
            'role_name' => $user->roleName(),
            'deleted_at' => $user->deleted_at,
        ]);
    }

    /**
     * Update user password
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => $validated['password']]);

        return $this->success('Password updated successfully');
    }

    /**
     * Archive a user (soft delete)
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return $this->success('User archived successfully');
    }

    /**
     * Restore an archived user
     */
    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return $this->success('User restored successfully');
    }
}
