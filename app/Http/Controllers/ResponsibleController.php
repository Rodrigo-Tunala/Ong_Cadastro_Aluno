<?php

namespace App\Http\Controllers;

use App\Models\Responsible;
use App\Services\ResponsibleService;
use Illuminate\Http\Request;

class ResponsibleController extends Controller
{

    private ResponsibleService $responsibleService;

    public function __construct(ResponsibleService $responsibleService)
    {
        $this->responsibleService = $responsibleService;
    }
    

    public function index()
    {
        $responsibles = $this->responsibleService->getAllResponsibles();

        return response()->json([
            'message' => 'Lista de responsáveis',
            'data' => $responsibles
        ], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cpf' => 'required|string|max:14|unique:responsibles',
        ]);

        try {
            $responsible = $this->responsibleService->createResponsible($validated);

            return response()->json([
                'message' => 'Responsável criado com sucesso',
                'data' => $responsible
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar responsável',
                'error' => $e->getMessage()
            ], 500);

        }
            
    }

    /**
     * Display the specified resource.
     */
    public function show(Responsible $responsible)
    {
        $responsible = $this->responsibleService->getResponsibleById($responsible->id);

        return response()->json([
            'message' => 'Detalhes do responsável',
            'data' => $responsible
        ], 200);
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Responsible $responsible)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Responsible $responsible)
    {
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cpf' => 'required|string|unique:responsibles|max:14',
            ]);
            
        try{
            $responsible = $this->responsibleService->updateResponsible($responsible, $validated);
            
            return response()->json([
                'message' => 'Responsável atualizado com sucesso',
                'data' => $responsible
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar responsável',
                'error' => $e->getMessage()
            ], 500);

        }

        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Responsible $responsible)
    {

        try {
            $this->responsibleService->deleteResponsible($responsible);
        
            return response()->json([
                'message' => 'Responsável deletado com sucesso'
            ], 200);    
        }catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar responsável',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
