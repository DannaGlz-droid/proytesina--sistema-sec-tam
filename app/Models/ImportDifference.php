<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportDifference extends Model
{
    protected $fillable = [
        'import_id',
        'death_id',
        'source_import_id',
        'gov_folio',
        'previous_values',
        'proposed_values',
        'changed_fields',
        'status',
        'field_decisions',
        'resolved_by_user_id',
        'resolved_at',
    ];

    protected $casts = [
        'previous_values' => 'array',
        'proposed_values' => 'array',
        'changed_fields' => 'array',
        'field_decisions' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function death()
    {
        return $this->belongsTo(Death::class);
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }
}
