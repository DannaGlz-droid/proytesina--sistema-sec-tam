<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationFile extends Model
{
     /**
     * The table associated with the model.
     */
    protected $table = 'publication_files';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'publication_id',
        'original_name',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $hidden = [ // Protege datos sensibles en respuestas API
        'publication_id',  // ID de la publicación asociada
        'file_name',       // Nombre del archivo en el servidor
        'file_path',       // Ruta del archivo en el servidor
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'file_size' => 'integer',
    ];

    /**
     * Relationship: PublicationFile belongs to Publication
     */

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}
