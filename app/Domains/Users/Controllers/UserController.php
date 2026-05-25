<?php

namespace App\Domains\Users\Controllers;

use App\Domains\Users\Requests\UserRequest;
use App\Domains\Users\Requests\UpdateProfileRequest;
use App\Domains\Users\Requests\UpdatePasswordRequest;
use App\Domains\Users\Requests\LoginRequest;
use App\Domains\Users\Services\UserService;
use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Inertia\Inertia;

use Inertia\Response as InertiaResponse;

class UserController extends Controller
{

    private function renderProfile(string $title, Collection $roles, User $user, bool $isCurrentUser, bool $isAdmin = false): InertiaResponse
    {
        return Inertia::render('User/Profile', [
            'title' => $title,
            'user' => $user->load('role'),
            'isCurrentUser' => $isCurrentUser,
            'isAdmin' => $isAdmin,
            'roles' => $roles,
        ]);
    }

    public function showSignup(): InertiaResponse
    {
        return Inertia::render('Auth/Signup', [
            'title' => 'Registrarse',
        ]);
    }

    public function signup(UserRequest $request, UserService $userService): RedirectResponse
    {
        $user = $userService->register($request->validatedUserData());
        if(! $user) {
            return back()->with('error', 'No se pudo registrar el usuario. Inténtalo de nuevo.');
        }
        Auth::login($user);

        $request->session()->regenerate();

        return to_route('home');
    }

    public function showLogin(): InertiaResponse
    {
        return Inertia::render('Auth/Login', [
            'title' => 'Iniciar Sesión',
        ]);
    }

    public function login(LoginRequest $request, UserService $userService): RedirectResponse
    {
        $credentials = $request->validatedLoginData();

        if (! $userService->authenticate($credentials['username'], $credentials['password'], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'username' => 'Las credenciales ingresadas no coinciden con nuestros registros.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function showSettings(): InertiaResponse
    {
        return Inertia::render('User/Settings', [
            'title' => 'Ajustes de Usuario',
        ]);
    }

    public function showUsers(UserService $userService): InertiaResponse
    {
        return Inertia::render('User/Index', [
            'title' => 'Usuarios',
            'users' => $userService->getAllUsers(),
        ]);
    }

    public function showUser(int $id, UserService $userService): InertiaResponse
    {
        $user = $userService->getUserById($id);
        if (! $user) {
            abort(404);
        }
        $isCurrentUser = Auth::id() === $user->id;
        $isAdmin = Auth::user()->role_id === $userService->getAdminId();
        return $this->renderProfile("Perfil de {$user->name} {$user->surname}", $userService->getAllRoles(), $user, $isCurrentUser, $isAdmin);
    }

    public function showProfile(UserService $userService): InertiaResponse
    {
        $isAdmin = Auth::user()->role_id === $userService->getAdminId();
        $user = $userService->getAuthenticatedUser();

        if (! $user) {
            abort(403);
        }

        return $this->renderProfile('Mi Perfil', $userService->getAllRoles(), $user, true, $isAdmin);
    }

    public function updateProfile(UpdateProfileRequest $request, UserService $userService): RedirectResponse
    {
        $user = $userService->updateProfile($request->id, $request->validatedProfileData());
        if ($user) {
            return back()->with('success', 'Perfil actualizado correctamente.');
        }else {
            return back()->with('error', 'No se pudo actualizar el perfil. Inténtalo de nuevo.');
        }

    }

    public function updatePassword(UpdatePasswordRequest $request, UserService $userService): RedirectResponse
    {
        $data = $request->validatedPasswordData();
        $userId = $userService->getAuthenticatedUser()->id;

        if (! $userService->updatePassword($userId, $data['current_password'], $data['new_password'])) {
            throw ValidationException::withMessages([
                'current_password' => 'La contraseña actual es incorrecta.',
            ]);
        }

        return to_route('profile')->with('success', 'Contraseña actualizada correctamente.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login');
    }
}
