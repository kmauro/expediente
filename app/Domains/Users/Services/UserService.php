<?php

namespace App\Domains\Users\Services;

use App\Domains\Users\Models\User;
use App\Domains\Users\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(protected UserRepository $userRepository) {}

    /**
     * @param  array{name: string, email: string, password: string, surname: string, username: string, phone: string}  $data
     */
    public function register(array $data): User
    {
        $user = $this->userRepository->create($data);
        if(! $user) {
            throw new \Exception('Error al registrar el usuario');
        }
        return $user;
    }

    public function authenticate(string $username, string $password, bool $remember = false): bool
    {
        return Auth::attempt([
            'username' => $username,
            'password' => $password,
        ], $remember);
    }

    public function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    public function getAllUsers(): Collection
    {
        return $this->userRepository->all();
    }

    public function getAllRoles(): Collection
    {
        return $this->userRepository->getAllRoles();
    }

    public function getUserById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function updateProfile(int $userId, array $data): User
    {
        $user = $this->userRepository->update($userId, $data);
        if(! $user) {
            throw new \Exception('Error al actualizar el perfil');
        }
        return $user;
    }

    public function updatePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->find($userId);

        if (! Auth::validate(['username' => $user->username, 'password' => $currentPassword])) {
            return false;
        }

        $user = $this->userRepository->update($userId, ['password' => $newPassword]);
        if(! $user) {
            throw new \Exception('Error al actualizar la contraseña');
        }

        return true;
    }
}
