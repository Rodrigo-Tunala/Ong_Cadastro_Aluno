<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getAllUsers()
    {
        return User::get();
    }

    public function getUserById(int $id)
    {
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'rg' => $data['rg'] ?? null,
                'cpf' => $data['cpf'] ?? null,
                'address' => $data['address'] ?? null,
                'neighborhood' => $data['neighborhood'] ?? null,
                'city' => $data['city'] ?? null,
                'cep' => $data['cep'] ?? null,
                'phone' => $data['phone'] ?? null,
                'type' => $data['type'] ?? 'voluntary1',
            ]);

            return $user;
        });
    }

    public function updateUser(User $user, array $data, ?User $currentUser = null)
    {
        return DB::transaction(function () use ($user, $data, $currentUser) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }

            if (isset($data['type'])) {
                if (! $currentUser || $currentUser->type !== 'admin') {
                    throw new \Exception('Apenas administradores podem alterar o tipo de usuário.');
                }
            }

            $allowedFields = [
                'name',
                'email',
                'password',
                'rg',
                'cpf',
                'address',
                'neighborhood',
                'city',
                'cep',
                'phone',
                'type',
            ];

            $updateData = array_intersect_key($data, array_flip($allowedFields));
            $user->update($updateData);

            return $user;
        });
    }

    public function deleteUser(User $user)
    {
        return DB::transaction(function () use ($user) {
            $user->delete();
            return true;
        });
    }

    public function loginUser(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        /** @var \App\Models\User $user */
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logoutUser(User $user)
    {
        return DB::transaction(function () use ($user) {
            $user->tokens()->delete();
            return true;
        });
    }

    public function restoreUser(int $id)
    {
        return DB::transaction(function () use ($id) {
            $user = User::withTrashed()->findOrFail($id);
            if (! $user->trashed()) {
                throw new \Exception('Usuário não está deletado.');
            }
            if (Auth::check() && Auth::user()->type !== 'admin') {
                throw new \Exception('Apenas administradores podem restaurar usuários.');
            }
            $user->restore();
            return $user;
        });
    }
}