<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'message' => 'Lista de usuários',
            'data' => $users,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'rg' => 'nullable|string|max:12|unique:users',
            'cpf' => 'nullable|string|max:14|unique:users',
            'address' => 'nullable|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:20',
            'type' => 'string|in:admin,voluntary2,voluntary1',
        ]);

        try {
            $user = $this->userService->createUser($validatedData);

            return response()->json([
                'message' => 'Usuário criado com sucesso',
                'data' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!Auth::check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $currentUser = Auth::user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'rg' => 'sometimes|nullable|string|max:12|unique:users,rg,' . $user->id,
            'cpf' => 'sometimes|nullable|string|max:14|unique:users,cpf,' . $user->id,
            'address' => 'sometimes|string|max:255',
            'neighborhood' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'cep' => 'sometimes|string|max:10',
            'phone' => 'sometimes|string|max:20',
            'type' => 'sometimes|string|in:admin,voluntary2,voluntary1',
        ]);

        try {
            $user = $this->userService->updateUser($user, $validated, $currentUser);

            return response()->json([
                'message' => 'Usuário atualizado com sucesso',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if (!Auth::check()) {
            return response()->json(['error' => 'Não autenticado'], 401);
        }

        $currentUser = Auth::user();

        if ($currentUser->id !== $user->id && $currentUser->type !== 'admin') {
            return response()->json([
                'error' => 'Apenas administradores podem deletar outros usuários',
            ], 403);
        }

        try {
            $this->userService->deleteUser($user);

            return response()->json([
                'message' => 'Usuário deletado com sucesso',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $result = $this->userService->loginUser($credentials);

        if (! $result) {
            return response()->json(['error' => 'Credenciais inválidas'], 401);
        }

        return response()->json([
            'message' => 'Login bem-sucedido',
            'access_token' => $result['token'],
            'token_type' => 'Bearer',
            'user' => $result['user'],
        ], 200);
    }
    
    public function logout(Request $request)
    {
        $user = $request->user();

        try {
            $this->userService->logoutUser($user);

            return response()->json([
                'message' => 'Logout bem-sucedido',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao fazer logout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function restore($id)
    {
        $user = Auth::user();

        if ($user->type !== 'admin') {
            return response()->json([
                'message' => 'Acesso negado. Somente administradores podem restaurar usuários.'
            ], 403);
        }


        try {
            $user = $this->userService->restoreUser($id);

            return response()->json([
                'message' => 'Usuário restaurado com sucesso',
                'data' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao restaurar usuário',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
