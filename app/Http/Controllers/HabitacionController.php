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
            // Validación de los datos
            $request->validate([
                "nombre" => "required|string",
                "tipo" => "required|string",
                "capacidad" => "required|numeric",
                "descripcion" => "nullable|string",
                "precio" => "required|numeric",
            ]);        
            // Crear la instancia de Habitacion
            $habitacion = new Habitacion();
            $habitacion->nombre = $request->input("nombre");
            $habitacion->tipo = $request->input("tipo");
            $habitacion->capacidad = $request->input("capacidad");
            $habitacion->descripcion = $request->input("descripcion") ?? null;
            $habitacion->precio = $request->input("precio");
            $habitacion->save(); // Guardar en la tabla de habitaciones

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
    public function update(Request $request, $id)
    {
        try {
            // Buscar la habitación por ID
            $habitacion = Habitacion::findOrFail($id);

            // Validar los datos de la solicitud
            $request->validate([
                'nombre' => 'required|string',
                'tipo' => 'required|string',
                'capacidad' => 'required|numeric',
                'descripcion' => 'nullable|string',
                'precio' => 'required|numeric',
            ]);

            // Actualizar la habitación con los nuevos datos
            $habitacion->update([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'capacidad' => $request->capacidad,
                'descripcion' => $request->descripcion,
                'precio' => $request->precio,
            ]);

            // Eliminar imágenes existentes
            if ($habitacion->imagenes) {
                foreach ($habitacion->imagenes as $image) {
                    $imagePath = public_path('images/habitacions/' . $image->nombre);
                    if (file_exists($imagePath)) {
                        unlink($imagePath); // Eliminar archivo físico
                    }
                    $image->delete(); // Eliminar registro en la base de datos
                }
            }

            // Agregar nuevas imágenes si están presentes en la solicitud
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $img) {
                    $imageName = time() . '_' . $img->getClientOriginalName();
                    $img->move(public_path('images/habitacions/'), $imageName);

                    $image = new Imagen();
                    $image->nombre = $imageName;
                    $image->habitacion_id = $habitacion->id;
                    $image->save();
                }
            }

            // Obtener la habitación actualizada con sus relaciones
            $habitacionActualizada = Habitacion::with(['imagenes'])->findOrFail($habitacion->id);

            return response()->json(['habitacion' => $habitacionActualizada, 'message' => 'Habitación actualizada correctamente'], 200);
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