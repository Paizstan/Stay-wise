<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Imagen;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return response()->json(Habitacion::with( 'imagenes')->get());
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
    {
        try {
            // Si los datos vienen en un campo 'habitaciones' como JSON, los decodificamos
            $habitacionRequest = $request->input("habitaciones") ? json_decode($request->input("habitaciones"), true) : $request->all();

            // Validación de los datos
            $validator = Validator::make($habitacionRequest, [
                "nombre" => "required|string",
                "tipo" => "required|in:Individual,Doble,Doble estándar,Apartamento,Suite,Suite ejecutiva,Suite presidencial",
                "capacidad" => "required|in:1,2,4,6",
                "descripcion" => "nullable|string",
                "precio" => "required|numeric",
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Verificar si la habitación ya existe en la base de datos
            $existe = Habitacion::where('nombre', $habitacionRequest["nombre"])
                ->where('tipo', $habitacionRequest["tipo"])
                ->where('capacidad', $habitacionRequest["capacidad"])
                ->first();

            if ($existe) {
                return response()->json(['message' => 'Ya existe una habitación con estos datos'], 409);
            }

            // Crear la instancia de Habitacion
            $habitacion = Habitacion::create([
                "nombre" => $habitacionRequest["nombre"],
                "tipo" => $habitacionRequest["tipo"],
                "capacidad" => $habitacionRequest["capacidad"],
                "descripcion" => $habitacionRequest["descripcion"] ?? null,
                "precio" => $habitacionRequest["precio"]
            ]);

            // Manejo de imágenes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $img) {
                    $imageName = time() . '_' . $img->getClientOriginalName();
                    $img->move(public_path('images/habitacions/'), $imageName);

                    Imagen::create([
                        'nombre' => $imageName,
                        'habitacion_id' => $habitacion->id
                    ]);
                }
            }

            return response()->json([
                "habitacion" => $habitacion->load('imagenes'),
                "message" => "Habitación registrada con éxito...!"
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            return response()->json(Habitacion::with('imagenes')->findOrFail($id));
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo encontrar la reserva: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Habitacion $habitacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Habitacion $habitacion)
{
    try {
        // Si los datos vienen en un campo 'habitaciones' como JSON, los decodificamos
        $habitacionRequest = $request->input("habitaciones") ? json_decode($request->input("habitaciones"), true) : $request->all();

        // Validación de los datos
        $validator = Validator::make($habitacionRequest, [
            "nombre" => "required|string",
            "tipo" => "required|in:Individual,Doble,Doble estándar,Apartamento,Suite,Suite ejecutiva,Suite presidencial",
            "capacidad" => "required|in:1,2,4,6",
            "descripcion" => "nullable|string",
            "precio" => "required|numeric",
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Actualizar los datos de la habitación
        $habitacion->update([
            "nombre" => $habitacionRequest["nombre"],
            "tipo" => $habitacionRequest["tipo"],
            "capacidad" => $habitacionRequest["capacidad"],
            "descripcion" => $habitacionRequest["descripcion"] ?? null,
            "precio" => $habitacionRequest["precio"]
        ]);

        // Manejo de imágenes
        if ($request->hasFile('imagenes')) {
            // Eliminar imágenes antiguas
            foreach ($habitacion->imagenes as $imagen) {
                $imagePath = public_path('images/habitacions/' . $imagen->nombre);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $imagen->delete();
            }

            // Guardar nuevas imágenes
            foreach ($request->file('imagenes') as $img) {
                $imageName = time() . '_' . $img->getClientOriginalName();
                $img->move(public_path('images/habitacions/'), $imageName);

                Imagen::create([
                    'nombre' => $imageName,
                    'habitacion_id' => $habitacion->id
                ]);
            }
        }

        return response()->json([
            "habitacion" => $habitacion->load('imagenes'),
            "message" => "Habitación actualizada con éxito...!"
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $habitacion = Habitacion::findOrFail($id);

            foreach ($habitacion->imagenes as $image) {
                $imagePath = public_path('images/habitacions/' . $image->nombre);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            // Eliminamos las imágenes de la base de datos
            $habitacion->imagenes()->delete();
            // Eliminamos la habitación
            if ($habitacion->delete()) {
                return response()->json(["message" => "Habitación eliminado"], 200);
            }
            return response()->json(["message" => "Ocurrió un error al eliminar la Habitación"], 409);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}