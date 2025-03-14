<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            return response()->json(Reserva::all());
        }catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()],500);

        }
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
        try {  
            // Validar los datos de entrada  
            $request->validate([  
                'fecha_creacion' => 'required|date',  
                'estado' => 'required|in:Confirmado,Pendiente,Cancelada',  
                'pagada' => 'boolean',  
                'user_id' => 'required|exists:users,id',  
            ]);  

            // Crear una nueva reserva  
            $reserva = new Reserva();  
            $reserva->fecha_creacion = $request->fecha_creacion;  
            $reserva->estado = $request->estado;  
            $reserva->pagada = $request->pagada ?? false; // Por defecto es false  
            $reserva->user_id = $request->user_id;  
            $reserva->save();  

            // Retornar una respuesta exitosa  
            return response()->json(['message' => 'Reserva creada exitosamente.'], 201);  
        } catch (\Illuminate\Validation\ValidationException $e) {  
            // Manejar errores de validación  
            return response()->json(['errors' => $e->validator->errors()], 422);  
        } catch (\Exception $e) {  
            // Manejar otros errores  
            return response()->json(['error' => 'No se pudo crear la reserva: ' . $e->getMessage()], 500);  
        }  
    }  

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json(Reserva::with('user')->findOrFail($id));
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo encontrar la reserva: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'fecha_creacion' => 'required|date',
                'estado' => 'required|in:Confirmado,Pendiente,Cancelada',
                'pagada' => 'boolean',
                'user_id' => 'required|exists:users,id',
            ]);
    
            // Actualizar la reserva con los nuevos valores
            $reserva->fecha_creacion = $request->fecha_creacion;
            $reserva->estado = $request->estado;
            $reserva->pagada = $request->pagada ?? false;
            $reserva->user_id = $request->user_id;
            $reserva->save();
    
            return response()->json(['message' => 'Reserva actualizada exitosamente.', 'data' => $reserva]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo actualizar la reserva: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        try {
            $reserva->delete();
            return response()->json(['message' => 'Reserva eliminada exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo eliminar la reserva: ' . $e->getMessage()], 500);
        }
    }

    public function getDatos()  
    {  
        $reservas = Reserva::with('user')->get(); // Ensure the relationship is 'user'
        return response()->json($reservas);  
    }
}
