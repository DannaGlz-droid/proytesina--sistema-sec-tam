<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\PublicationFile;
use App\Models\PublicationComment;
use App\Models\RoadSafetyReport;
use App\Models\InjuryObservatoryReport;
use App\Models\BreathalyzerReport;
use App\Models\Municipality;
use App\Models\Jurisdiction;
use App\Models\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Mostrar todas las publicaciones/reportes
     */
    public function index()
    {
        $query = Publication::with([
            'user',
            'files',
            'comments.user.position',
            'roadSafetyReports.activityType',
            'injuryObservatoryReports.municipality',
            'injuryObservatoryReports.jurisdiction',
            'breathalyzerReports'
        ]);

        // Filtrar según el rol del usuario
        $user = Auth::user();
        if ($user->isOperator()) {
            // Operadores solo ven sus propias publicaciones
            $query->where('user_id', $user->id);
        }
        // Admin, Coordinador e Invitado ven todas las publicaciones

        $publications = $query->orderBy('created_at', 'desc')->get();

        // Preparar comentarios en formato JSON para cada publicación
        $publications->each(function ($pub) use ($user) {
            // Ensure comments are ordered chronologically (oldest first)
            $orderedComments = $pub->comments->sortBy('created_at')->values();

            // For now, force presentation timezone to Victoria, Tamaulipas (central Mexico)
            // This will show all comment dates/times in the same timezone.
            $viewerTz = 'America/Mexico_City';

            $pub->comentarios_json = $orderedComments->map(function ($c) use ($user, $viewerTz) {
                return [
                    'id' => $c->id,
                    'comment' => $c->comment,
                    // Provide separate date and time fields formatted to the viewer's timezone
                    'date' => $c->created_at->setTimezone($viewerTz)->format('d/m/Y'),
                    'time' => $c->created_at->setTimezone($viewerTz)->format('H:i'),
                    'created_at' => $c->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY, h:mm A'),
                    // ISO timestamp (includes timezone) so the frontend can format to the user's/local timezone
                    'created_at_iso' => $c->created_at->toIso8601String(),
                    'user' => [
                        'id' => $c->user->id,
                        'name' => $c->user->name,
                        'position' => optional($c->user->position)->name ?? 'Sin cargo'
                    ],
                    // Per-user seen flag
                    // - If the current viewer is the author, show whether OTHER users have read this comment
                    //   (this yields WhatsApp-like double-check for the author when others have seen it).
                    // - Otherwise, show whether the current viewer has read it.
                    'seen_by_current_user' => (
                        $c->user_id === $user->id
                        ? (
                            \Schema::hasTable('comment_reads')
                            ? $c->reads()->where('user_id', '!=', $user->id)->exists()
                            : false
                        )
                        : $c->isReadByUser($user->id)
                    ),
                    'can_delete' => Auth::id() === $c->user_id || Auth::user()->isAdmin()
                ];
            });
        });
        
        return view('reportes.publicaciones', compact('publications'));
    }

    /**
     * Eliminar una publicación y sus archivos y reportes relacionados.
     */
    public function destroy(Publication $publication)
    {
        try {
            DB::beginTransaction();

            // Borrar archivos físicos y registros en publication_files
            foreach ($publication->files as $file) {
                // file_path se guardó con el driver 'public' (p.ej. reportes/..)
                Storage::disk('public')->delete($file->file_path);
                $file->delete();
            }

            // Borrar reportes específicos asociados
            RoadSafetyReport::where('publication_id', $publication->id)->delete();
            InjuryObservatoryReport::where('publication_id', $publication->id)->delete();
            BreathalyzerReport::where('publication_id', $publication->id)->delete();

            // Finalmente borrar la publicación
            $publication->delete();

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Publicación eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('reportes.index')
                ->with('error', 'Error al eliminar la publicación: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un archivo individual de una publicación
     */
    public function destroyFile(PublicationFile $file)
    {
        try {
            // Guardar el ID de la publicación para redireccionar después
            $publication = $file->publication;
            
            // Determinar la ruta de edición según el tipo de publicación
            $editRoute = match($publication->publication_type) {
                'seguridad_vial' => route('reportes.seguridad-vial.edit', $publication),
                'observatorio' => route('reportes.observatorio.edit', $publication),
                'alcoholimetria' => route('reportes.alcoholimetria.edit', $publication),
                default => route('reportes.index')
            };

            // Borrar archivo físico del disco
            Storage::disk('public')->delete($file->file_path);
            
            // Borrar registro de la base de datos
            $file->delete();

            return redirect($editRoute)
                ->with('success', 'Archivo eliminado correctamente.');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de Observatorio de Lesiones
     */
    public function createObservatorio()
    {
        $municipalities = Municipality::with('jurisdiction')->orderBy('name')->get();
        $jurisdictions = Jurisdiction::orderBy('name')->get();
        
        return view('reportes.registro.observatorio-de-lesiones', compact('municipalities', 'jurisdictions'));
    }

    /**
     * Mostrar formulario de Seguridad Vial
     */
    public function createSeguridadVial()
    {
        $activityTypes = ActivityType::all();
        
        return view('reportes.registro.seguridad-vial', compact('activityTypes'));
    }

    /**
     * Almacenar un reporte de Seguridad Vial
     */
    public function storeSeguridadVial(Request $request)
    {
        // Obtener user_id (autenticado o usuario por defecto)
        $userId = Auth::id() ?? \App\Models\User::first()->id;
        
        // DEBUG: Ver qué datos llegan
        \Log::info('Datos recibidos en storeSeguridadVial:', [
            'user_id' => $userId,
            'all_data' => $request->all(),
            'activity_type_id' => $request->activity_type_id,
            'has_archivos' => $request->hasFile('archivos'),
            'archivos_count' => $request->hasFile('archivos') ? count($request->file('archivos')) : 0,
        ]);

        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'activity_type_id' => 'required|exists:activity_types,id',
            'participantes' => 'required|integer|min:1',
            'lugar' => 'required|string|max:255',
            'promotor' => 'required|string|max:255',
            'archivos.*' => 'required|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:10240', // 10MB por archivo
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la publicación
            $publication = Publication::create([
                'user_id' => $userId,
                'publication_type' => 'seguridad_vial',
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'publication_date' => now(),
                'activity_date' => $validated['fecha'],
                'status' => 'publicado',
            ]);

            // 2. Crear el reporte específico de Seguridad Vial
            RoadSafetyReport::create([
                'publication_id' => $publication->id,
                'activity_type_id' => $validated['activity_type_id'],
                'participants' => $validated['participantes'],
                'location' => $validated['lugar'],
                'promoter' => $validated['promotor'],
            ]);

            // 3. Guardar archivos si existen (soporte para múltiples archivos)
            if ($request->hasFile('archivos')) {
                $files = $request->file('archivos');
                foreach ($files as $file) {
                    $this->storeFile($file, $publication, 'seguridad_vial');
                }
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Seguridad Vial registrado exitosamente. Total de publicaciones: ' . Publication::count());

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición de Seguridad Vial
     */
    public function editSeguridadVial(Publication $publication)
    {
        // Verificar que sea del tipo correcto
        if ($publication->publication_type !== 'seguridad_vial') {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'Esta publicación no es de tipo Seguridad Vial');
        }

        $report = $publication->roadSafetyReports->first();
        $activityTypes = \App\Models\ActivityType::all();
        
        return view('reportes.registro.seguridad-vial', compact('publication', 'report', 'activityTypes'));
    }

    /**
     * Actualizar un reporte de Seguridad Vial
     */
    public function updateSeguridadVial(Request $request, Publication $publication)
    {
        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'activity_type_id' => 'required|exists:activity_types,id',
            'participantes' => 'required|integer|min:1',
            'lugar' => 'required|string|max:255',
            'promotor' => 'required|string|max:255',
            'archivos.*' => 'nullable|file|mimes:pdf,xlsx,xls,jpg,jpeg,png|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar la publicación
            $publication->update([
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'activity_date' => $validated['fecha'],
            ]);

            // 2. Actualizar el reporte específico
            $report = $publication->roadSafetyReports->first();
            if ($report) {
                $report->update([
                    'activity_type_id' => $validated['activity_type_id'],
                    'participants' => $validated['participantes'],
                    'location' => $validated['lugar'],
                    'promoter' => $validated['promotor'],
                ]);
            }

            // 3. Guardar nuevos archivos si existen
            if ($request->hasFile('archivos')) {
                $files = $request->file('archivos');
                foreach ($files as $file) {
                    $this->storeFile($file, $publication, 'seguridad_vial');
                }
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Seguridad Vial actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un reporte de Observatorio de Lesiones
     */
    public function storeObservatorio(Request $request)
    {
        // Obtener user_id (autenticado o usuario por defecto)
        $userId = Auth::id() ?? \App\Models\User::first()->id;
        
        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'municipio' => 'required|exists:municipalities,id',
            'jurisdiccion' => 'required|exists:jurisdictions,id',
            'archivo' => 'nullable|file|mimes:xlsx,xls|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la publicación
            $publication = Publication::create([
                'user_id' => $userId,
                'publication_type' => 'observatorio',
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'publication_date' => now(),
                'activity_date' => $validated['fecha'],
                'status' => 'publicado',
            ]);

            // 2. Crear el reporte específico de Observatorio
            InjuryObservatoryReport::create([
                'publication_id' => $publication->id,
                'municipality_id' => $validated['municipio'],
                'jurisdiction_id' => $validated['jurisdiccion'],
            ]);

            // 3. Guardar archivo si existe
            if ($request->hasFile('archivo')) {
                $this->storeFile($request->file('archivo'), $publication, 'observatorio');
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Observatorio de Lesiones registrado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición de Observatorio de Lesiones
     */
    public function editObservatorio(Publication $publication)
    {
        // Verificar que sea del tipo correcto
        if ($publication->publication_type !== 'observatorio') {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'Esta publicación no es de tipo Observatorio');
        }

        $report = $publication->injuryObservatoryReports->first();
        $municipalities = Municipality::with('jurisdiction')->orderBy('name')->get();
        $jurisdictions = Jurisdiction::orderBy('name')->get();
        
        return view('reportes.registro.observatorio-de-lesiones', compact('publication', 'report', 'municipalities', 'jurisdictions'));
    }

    /**
     * Actualizar un reporte de Observatorio de Lesiones
     */
    public function updateObservatorio(Request $request, Publication $publication)
    {
        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'municipio' => 'required|exists:municipalities,id',
            'jurisdiccion' => 'required|exists:jurisdictions,id',
            'archivo' => 'nullable|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar la publicación
            $publication->update([
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'activity_date' => $validated['fecha'],
            ]);

            // 2. Actualizar el reporte específico
            $report = $publication->injuryObservatoryReports->first();
            if ($report) {
                $report->update([
                    'municipality_id' => $validated['municipio'],
                    'jurisdiction_id' => $validated['jurisdiccion'],
                ]);
            }

            // 3. Guardar nuevo archivo si existe
            if ($request->hasFile('archivo')) {
                $this->storeFile($request->file('archivo'), $publication, 'observatorio');
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Observatorio actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Almacenar un reporte de Alcoholimetría
     */
    public function storeAlcoholimetria(Request $request)
    {
        // Obtener user_id (autenticado o usuario por defecto)
        $userId = Auth::id() ?? \App\Models\User::first()->id;
        
        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'puntos_revision' => 'required|integer|min:0',
            'pruebas_realizadas' => 'required|integer|min:0',
            'conductores_no_aptos' => 'required|integer|min:0',
            'mujeres_no_aptas' => 'required|integer|min:0',
            'hombres_no_aptos' => 'required|integer|min:0',
            'automoviles_camionetas' => 'required|integer|min:0',
            'motocicletas' => 'required|integer|min:0',
            'transporte_colectivo' => 'required|integer|min:0',
            'transporte_individual' => 'required|integer|min:0',
            'transporte_carga' => 'required|integer|min:0',
            'vehiculos_emergencia' => 'required|integer|min:0',
            'archivo' => 'nullable|file|mimes:xlsx,xls|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear la publicación
            $publication = Publication::create([
                'user_id' => $userId,
                'publication_type' => 'alcoholimetria',
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'publication_date' => now(),
                'activity_date' => $validated['fecha'],
                'status' => 'publicado',
            ]);

            // 2. Crear el reporte específico de Alcoholimetría
            BreathalyzerReport::create([
                'publication_id' => $publication->id,
                'checkpoints' => $validated['puntos_revision'],
                'tests_performed' => $validated['pruebas_realizadas'],
                'drivers_not_fit' => $validated['conductores_no_aptos'],
                'women' => $validated['mujeres_no_aptas'],
                'men' => $validated['hombres_no_aptos'],
                'cars_trucks' => $validated['automoviles_camionetas'],
                'motorcycles' => $validated['motocicletas'],
                'public_transport_collective' => $validated['transporte_colectivo'],
                'public_transport_individual' => $validated['transporte_individual'],
                'cargo_transport' => $validated['transporte_carga'],
                'emergency_vehicles' => $validated['vehiculos_emergencia'],
            ]);

            // 3. Guardar archivo si existe
            if ($request->hasFile('archivo')) {
                $this->storeFile($request->file('archivo'), $publication, 'alcoholimetria');
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Alcoholimetría registrado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al registrar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición de Alcoholimetría
     */
    public function editAlcoholimetria(Publication $publication)
    {
        // Verificar que sea del tipo correcto
        if ($publication->publication_type !== 'alcoholimetria') {
            return redirect()
                ->route('reportes.index')
                ->with('error', 'Esta publicación no es de tipo Alcoholimetría');
        }

        $report = $publication->breathalyzerReports->first();
        
        return view('reportes.registro.alcoholimetria', compact('publication', 'report'));
    }

    /**
     * Actualizar un reporte de Alcoholimetría
     */
    public function updateAlcoholimetria(Request $request, Publication $publication)
    {
        // Validación
        $validated = $request->validate([
            'tema' => 'required|string|max:255',
            'fecha' => 'required|date',
            'puntos_revision' => 'required|integer|min:0',
            'pruebas_realizadas' => 'required|integer|min:0',
            'conductores_no_aptos' => 'required|integer|min:0',
            'mujeres_no_aptas' => 'required|integer|min:0',
            'hombres_no_aptos' => 'required|integer|min:0',
            'automoviles_camionetas' => 'required|integer|min:0',
            'motocicletas' => 'required|integer|min:0',
            'transporte_colectivo' => 'required|integer|min:0',
            'transporte_individual' => 'required|integer|min:0',
            'transporte_carga' => 'required|integer|min:0',
            'vehiculos_emergencia' => 'required|integer|min:0',
            'archivo' => 'nullable|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar la publicación
            $publication->update([
                'topic' => $validated['tema'],
                'description' => $request->descripcion,
                'activity_date' => $validated['fecha'],
            ]);

            // 2. Actualizar el reporte específico
            $report = $publication->breathalyzerReports->first();
            if ($report) {
                $report->update([
                    'checkpoints' => $validated['puntos_revision'],
                    'tests_performed' => $validated['pruebas_realizadas'],
                    'drivers_not_fit' => $validated['conductores_no_aptos'],
                    'women' => $validated['mujeres_no_aptas'],
                    'men' => $validated['hombres_no_aptos'],
                    'cars_trucks' => $validated['automoviles_camionetas'],
                    'motorcycles' => $validated['motocicletas'],
                    'public_transport_collective' => $validated['transporte_colectivo'],
                    'public_transport_individual' => $validated['transporte_individual'],
                    'cargo_transport' => $validated['transporte_carga'],
                    'emergency_vehicles' => $validated['vehiculos_emergencia'],
                ]);
            }

            // 3. Guardar nuevo archivo si existe
            if ($request->hasFile('archivo')) {
                $this->storeFile($request->file('archivo'), $publication, 'alcoholimetria');
            }

            DB::commit();

            return redirect()
                ->route('reportes.index')
                ->with('success', 'Reporte de Alcoholimetría actualizado exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar el reporte: ' . $e->getMessage());
        }
    }

    /**
     * Método auxiliar para guardar archivos
     */
    private function storeFile($file, $publication, $tipo)
    {
        // Generar nombre único
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        
        // Crear ruta organizada por tipo y año/mes
        $year = now()->format('Y');
        $month = now()->format('m');
        $folder = "reportes/{$tipo}/{$year}/{$month}";
        
        // Guardar archivo
        $path = $file->storeAs($folder, $fileName, 'public');
        
        // Guardar registro en la base de datos
        PublicationFile::create([
            'publication_id' => $publication->id,
            'original_name' => $originalName,
            'file_name' => $fileName,
            'file_path' => $path,
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);
    }

    /**
     * Guardar un nuevo comentario
     * - Admin/Coordinador: pueden comentar en cualquier publicación
     * - Operador: solo puede comentar en sus propias publicaciones (para responder)
     */
    public function storeComment(Request $request, Publication $publication)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $user = Auth::user();

        // Verificar permisos: Admin/Coordinador pueden comentar en todo
        // Operador solo puede comentar en sus propias publicaciones
        if ($user->isOperator() && $publication->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Solo puedes comentar en tus propias publicaciones.',
            ], 403);
        }

        $comment = PublicationComment::create([
            'publication_id' => $publication->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'seen' => false,
        ]);

        // Cargar relaciones para devolver al frontend
        $comment->load('user.position');

    // For now, force comment response timezone to Victoria, Tamaulipas
    $userTz = 'America/Mexico_City';

        // DO NOT mark the comment as read for the author yet
        // The author should see their own comment as "sent" (single check), not "seen" (double check)
        // It will only show double check when OTHER users have read it

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'comment' => $comment->comment,
                // Return separate date and time for client-side display (formatted to author's timezone)
                'date' => $comment->created_at->setTimezone($userTz)->format('d/m/Y'),
                'time' => $comment->created_at->setTimezone($userTz)->format('H:i'),
                'created_at' => $comment->created_at->locale('es')->isoFormat('D [de] MMMM [de] YYYY, h:mm A'),
                // Provide ISO timestamp for client-side timezone-correct formatting
                'created_at_iso' => $comment->created_at->toIso8601String(),
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                    'position' => $comment->user->position->name ?? 'Sin cargo',
                ],
                // Author sees their own comment as NOT seen yet (will show single check)
                'seen_by_current_user' => false,
                'can_delete' => Auth::id() === $comment->user_id || Auth::user()->isAdmin(),
            ],
        ]);
    }

    /**
     * Eliminar un comentario (solo el autor o Admin)
     */
    public function destroyComment(PublicationComment $comment)
    {
        // Verificar que el usuario sea el autor o un administrador
        if (Auth::id() !== $comment->user_id && !Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comentario eliminado correctamente.',
        ]);
    }

    /**
     * Marcar como vistos los comentarios de una publicación cuando un usuario abre la publicación.
     * Marca como seen = true todos los comentarios de la publicación que NO fueron escritos por el usuario actual.
     */
    public function markCommentsSeen(Publication $publication)
    {
        $user = Auth::user();
        // For per-user reads, create entries in comment_reads for comments not authored by the current user
        $comments = PublicationComment::where('publication_id', $publication->id)
            ->where('user_id', '!=', $user->id)
            ->get();

        $updatedIds = [];
        foreach ($comments as $c) {
            $created = \App\Models\CommentRead::firstOrCreate([
                'publication_comment_id' => $c->id,
                'user_id' => $user->id,
            ], [
                'seen_at' => now(),
            ]);

            // If the record was just created (fresh), include in updated ids
            if ($created->wasRecentlyCreated) {
                $updatedIds[] = $c->id;
            }
        }

        return response()->json([
            'success' => true,
            'updated_ids' => $updatedIds,
        ]);
    }
}
