<?php

namespace App\Services;

use App\Models\Responsible;
use Illuminate\Support\Facades\DB;

class ResponsibleService
{
    public function getAllResponsibles()
    {
        return Responsible::with('students')->get();
    }

    public function getResponsibleById(int $id)
    {
        return Responsible::with('students')->findOrFail($id);
    }

    public function createResponsible(array $data)
    {
        return DB::transaction(function () use ($data) {
            $responsible = Responsible::firstOrNew(
                [
                    'name' => $data['name'],
                    'cpf' => $data['cpf'],
                ]
            );

            $responsible->phone = $data['phone'] ?? $responsible->phone;
            $responsible->save();

            return $responsible->load('students');
        });
    }

    public function updateResponsible(Responsible $responsible, array $data)
    {
        return DB::transaction(function () use ($responsible, $data) {
            $responsible->update($data);
            return $responsible->load('students');
        });
    }

    public function deleteResponsible(Responsible $responsible)
    {
        return DB::transaction(function () use ($responsible) {
            $responsible->delete();
            return true;
        });
    }
}