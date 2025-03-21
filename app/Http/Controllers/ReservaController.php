<?php

namespace App\Http\Controllers;

use App\Models\DetalleReserva;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Log;


class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Load relationships for reservas
            $reservas = Reserva::with('detalleReservas', 'user')->get(); // Ensure 'usuario' is loaded
            return response()->json($reservas);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
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
    {   //Cambio Staley
    try {
        // Validación de datos
        $validData = $request->validate([
            'fecha_creacion' => 'required|date',
            'estado' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'detalle_reservas' => 'required|array|min:1',
            'detalle_reservas.*.fecha_entrada' => 'required|date',
            'detalle_reservas.*.fecha_salida' => 'required|date|after:detalle_reservas.*.fecha_entrada',
            'detalle_reservas.*.precio' => 'required|numeric|min:0',
            'detalle_reservas.*.habitacion_id' => 'required|exists:habitaciones,id',
        ]);

        // Iniciamos la transacción
        DB::beginTransaction();

        // Creación de la reserva
        $reserva = Reserva::create([
            'fecha_creacion' => $validData['fecha_creacion'],
            'estado' => $validData['estado'],
            'user_id' => $validData['user_id'],
        ]);

        // Preparar datos para inserción masiva
        $detalleData = collect($validData['detalle_reservas'])
            ->map(function ($det) use ($reserva) {
                return [
                    'fecha_entrada' => $det['fecha_entrada'],
                    'fecha_salida' => $det['fecha_salida'],
                    'precio' => $det['precio'],
                    'habitacion_id' => $det['habitacion_id'],
                    'reserva_id' => $reserva->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

        // Insertar detalles de la reserva
        DetalleReserva::insert($detalleData);

        // Confirmar transacción
        DB::commit();

        return response()->json([
            "reserva" => $reserva->load("detalleReservas"),
            "message" => "La reserva ha sido registrada correctamente"
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'error' => $e->errors(),
            "message" => "Error en la validación de los datos"
        ], 409);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Load relationships for a specific reserva
            $reserva = Reserva::with('detalleReservas.habitacion', 'user')->findOrFail($id);
            return response()->json($reserva);
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
    public function update(Request $request, $id)
{
    // Buscar la reserva
    $reserva = Reserva::find($id);

    if (!$reserva) {
        return response()->json(['error' => 'Reserva no encontrada'], 404);
    }

    // Validar el nuevo estado
    $validated = $request->validate([
        'estado' => 'required|string',
    ]);

    // Actualizar el estado de la reserva
    $reserva->estado = $validated['estado'];
    $reserva->save();

    return response()->json(['message' => 'Estado de la reserva actualizado'], 200);
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
