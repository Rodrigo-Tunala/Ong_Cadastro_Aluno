<?php

namespace App\Http\Controllers;

use App\Models\StudentMeasurement;
use App\Services\StudentMeasurementService;     
use Illuminate\Http\Request;

class StudentMeasurementController extends Controller
{
    private StudentMeasurementService $studentMeasurementService;

    public function __construct(StudentMeasurementService $studentMeasurementService)
    {
        $this->studentMeasurementService = $studentMeasurementService;
    }

    
    public function index()
    {
        $studentMeasurements = $this->studentMeasurementService->getAllStudentMeasurements();

        return response()->json([
            'message' => 'Lista de medições dos alunos',
            'data' => $studentMeasurements
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'shorts_size' => 'nullable|string|max:10',
            'shoe_size' => 'nullable|string|max:10',
            'shirt_size' => 'nullable|string|max:10',
        ]);

        try {
            $studentMeasurement = $this->studentMeasurementService->createStudentMeasurement($validated);

            return response()->json([
                'message' => 'Medição do aluno criada com sucesso',
                'data' => $studentMeasurement
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar medição do aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(StudentMeasurement $studentMeasurement)
    {
        $studentMeasurement = $this->studentMeasurementService->getStudentMeasurementById($studentMeasurement->id);

        return response()->json([
            'message' => 'Detalhes da medição do aluno',
            'data' => $studentMeasurement
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudentMeasurement $studentMeasurement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudentMeasurement $studentMeasurement)
    {

        $validated = $request->validate([
            'student_id' => 'sometimes|exists:students,id',
            'shorts_size' => 'sometimes|nullable|string|max:10',
            'shoe_size' => 'sometimes|nullable|string|max:10',
            'shirt_size' => 'sometimes|nullable|string|max:10',
        ]);

        try {
            $studentMeasurement = $this->studentMeasurementService->updateStudentMeasurement($studentMeasurement, $validated);

            return response()->json([
                'message' => 'Medição do aluno atualizada com sucesso',
                'data' => $studentMeasurement
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar medição do aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudentMeasurement $studentMeasurement)
    {
        try {
            $this->studentMeasurementService->deleteStudentMeasurement($studentMeasurement);

            return response()->json([
                'message' => 'Medição do aluno deletada com sucesso'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar medição do aluno',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
