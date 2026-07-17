<?php

namespace App\Services;

use App\Models\Responsible;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentService
{
   
    public function getAllStudents()
    {
        return Student::with('responsible', 'studentMeasurement')->get();
    }

   
    public function getStudentById(int $id)
    {
        return Student::with('responsible', 'studentMeasurement')->findOrFail($id);
    }

    
    public function createStudent(array $data)
    {
        return DB::transaction(function () use ($data) {

        
            $responsible = Responsible::withTrashed()->firstOrNew(
                [
                    'name' => $data['responsible_name'],
                    'cpf' => $data['responsible_cpf'],
                ]
            );

            $responsible->phone = $data['responsible_phone'];

            if ($responsible->trashed()) {
                $responsible->restore();
            }

            $responsible->save();

            $student = Student::create([
                'name' => $data['name'],
                'birth_date' => $data['birth_date'],
                'shift' => $data['shift'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'],
                'neighborhood' => $data['neighborhood'],
                'school' => $data['school'],
                'school_situation' => $data['school_situation'],
                'school_year' => $data['school_year'],
                'responsible_id' => $responsible->id,
            ]);

            $student->studentMeasurement()->create([
                'shirt_size' => $data['shirt_size'] ?? null,
                'shorts_size' => $data['shorts_size'] ?? null,
                'shoe_size' => $data['shoe_size'] ?? null,
            ]);

            

            return $student->load('responsible', 'studentMeasurement');

        });
    }

    
    public function updateStudent(Student $student, array $data)
    {
        return DB::transaction(function () use ($student, $data) {

            $studentData = collect($data)->except(['responsible', 'studentMeasurement'])->toArray();

            $student->update($studentData);

            if (isset($data['responsible'])) {
                // Busca responsável incluindo soft-deletados
                $responsible = Responsible::withTrashed()->firstOrNew(
                    [
                        'name' => $data['responsible']['name'],
                        'cpf' => $data['responsible']['cpf'] ,
                    ]
                );

                if (isset($data['responsible']['phone'])) {
                    $responsible->phone = $data['responsible']['phone'];
                }

                if ($responsible->trashed()) {
                    $responsible->restore();
                }

                $responsible->save();

                $student->update([
                    'responsible_id' => $responsible->id
                ]);
            }

            if (isset($data['studentMeasurement'])) {
                $student->studentMeasurement()->updateOrCreate(
                    ['student_id' => $student->id],
                    $data['studentMeasurement']
                );
            }

            return $student->load('responsible', 'studentMeasurement');
        });
    }


    public function deleteStudent(Student $student)
    {
        return DB::transaction(function () use ($student) {
            $responsible = $student->responsible;
            $student->delete();

            // Se o responsável ficou sem alunos, soft-deleta
            if ($responsible && $responsible->students()->count() === 0) {
                $responsible->delete();
            }
            

            return true;
        });
    }

    public function restoreStudent(int $id)
    {
        return DB::transaction(function () use ($id) {

            $student = Student::withTrashed()->findOrFail($id);
            $student->restore();

            $responsible = Responsible::withTrashed()->find($student->responsible_id);
            if ($responsible && $responsible->trashed()) {
                $responsible->restore();
            }

            return $student->load('responsible', 'studentMeasurement');

           
        });
    }


}
