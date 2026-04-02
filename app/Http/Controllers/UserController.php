<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $currentUser = Auth::user();

    
    if (!$currentUser instanceof \App\Models\User) {
        return response()->json(['error' => 'Não autenticado'], 401);
    }

    if ($request->has('type') && $currentUser->type !== 'admin') {
        return response()->json(['error' => 'Você não tem permissão para alterar o tipo'], 403);
    }

        $user->name = $request->name ?? $user->name;
        $user->rg = $request->rg ?? $user->rg;
        $user->cpf = $request->cpf ?? $user->cpf;
        $user->address = $request->address ?? $user->address;
        $user->neighborhood = $request->neighborhood ?? $user->neighborhood;
        $user->city = $request->city ?? $user->city;
        $user->cep = $request->cep ?? $user->cep;
        $user->phone = $request->phone ?? $user->phone;

        if ($currentUser->type === 'admin' && $request->has('type')) {
            $user->type = $request->type;
        }

        $user->save();

        return response()->json(['message' => 'Usuário atualizado com sucesso', 'data' => $user]);

    }

}
