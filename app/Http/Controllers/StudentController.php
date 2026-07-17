<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StudentService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    private StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = $this->studentService->getAllStudents();

        return response()->json([
            'message' => 'Lista de alunos',
            'data' => $students
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'shift' => 'required|in:am,pm',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:255',
            'neighborhood' => 'required|string|max:255',
            'school' => 'required|string|max:255',
            'school_situation' => 'required|string|max:255',
            'school_year' => 'required|string|max:255',
            'responsible_name' => 'required|string|max:255',
            'responsible_phone' => 'required|string|max:20',
            'responsible_cpf' => 'required|string|unique:responsibles|max:14',
            'shirt_size' => 'nullable|string|max:10',
            'shorts_size' => 'nullable|string|max:10',
            'shoe_size' => 'nullable|string|max:10',
        ]);

        try {
            $student = $this->studentService->createStudent($validatedData);

            return response()->json([
                'message' => 'Aluno criado com sucesso',
                'data' => $student
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student = $this->studentService->getStudentById($student->id);

        return response()->json([
            'message' => 'Detalhes do aluno',
            'data' => $student
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'birth_date' => 'sometimes|date',
            'shift' => 'sometimes|in:am,pm',
            'phone' => 'nullable|string|max:20',
            'address' => 'sometimes|string|max:255',
            'neighborhood' => 'sometimes|string|max:255',
            'school' => 'sometimes|string|max:255',
            'school_situation' => 'sometimes|string|max:255',
            'school_year' => 'sometimes|string|max:255',

            'responsible.name' => 'required|string|max:255',
            'responsible.phone' => 'required|string|max:20',
            'responsible.cpf' => 'required|string|unique:responsibles|max:14',

            'studentMeasurement.shirt_size' => 'sometimes|nullable|string|max:10',
            'studentMeasurement.shorts_size' => 'sometimes|nullable|string|max:10',
            'studentMeasurement.shoe_size' => 'sometimes|nullable|string|max:10',
        ]);

        try {
            $student = $this->studentService->updateStudent($student, $validated);

            return response()->json([
                'message' => 'Aluno atualizado com sucesso',
                'data' => $student
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar o aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $user = Auth::user();

        if ($user->type !== 'admin') {
            return response()->json([
                'message' => 'Acesso negado. Somente administradores podem deletar alunos.'
            ], 403);
        }

        try {
            $this->studentService->deleteStudent($student);

            return response()->json([
                'message' => 'Aluno deletado com sucesso'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar o aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        
    

    public function restore($id)
    {
        $user = Auth::user();

        if ($user->type !== 'admin') {
            return response()->json([
                'message' => 'Acesso negado. Somente administradores podem restaurar alunos.'
            ], 403);
        }

        try {
            $student = $this->studentService->restoreStudent($id);

            return response()->json([
                'message' => 'Aluno restaurado com sucesso',
                'data' => $student
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao restaurar o aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
