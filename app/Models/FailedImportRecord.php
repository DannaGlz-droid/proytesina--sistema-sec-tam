<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FailedImportRecord extends Model
{
    protected $table = 'failed_import_records';

    protected $fillable = [
        'import_id',
        'original_row_data',
        'error_message',
        'corrected_data',
        'status',
    ];

    protected $casts = [
        'original_row_data' => 'array',
        'corrected_data' => 'array',
    ];

    public function import()
    {
        return $this->belongsTo(\Illuminate\Database\Eloquent\Model::class, 'import_id');
    }
}
