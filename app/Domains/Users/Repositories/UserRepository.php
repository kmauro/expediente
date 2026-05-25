<?php

namespace App\Domains\Users\Repositories;

use App\Domains\Users\Models\User;
use App\Domains\Users\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(int $userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }

    public function find(int $userId): ?User
    {
        return User::with('role')->find($userId);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function all(): Collection
    {
        return User::with('role')->get();

    }

    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    public function getAdminId() : int
    {
        $admin = DB::table('roles')->where('role_name', '=', 'admin')->first();
        return $admin->id;
    }
}
