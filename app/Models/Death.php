<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Death extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'deaths';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'first_last_name',
        'second_last_name',
        'age',
        'age_years',
        'age_months',
        'gov_folio',
        'sex',
        'death_date',
        'residence_municipality_id',
        'jurisdiction_id',
        'death_municipality_id',
        'death_location_id',
        'death_cause_id',
    ];

    protected $hidden = [
        'residence_municipality_id',  // FK sensible
        'jurisdiction_id',            // FK sensible
        'death_municipality_id',      // FK sensible (te faltó este)
        'death_location_id',          // FK sensible
        'death_cause_id',             // FK sensible
        'name',                       // Información personal sensible
        'first_last_name',            // Información personal sensible
        'second_last_name'            // Información personal sensible
    ];

    /**
     * Append formatted name attributes for display (keeps raw stored values intact).
     * These will be available when the model is serialized (e.g. JSON responses) as
     * `name_formatted`, `first_last_name_formatted`, `second_last_name_formatted`.
     */
    protected $appends = [
        'name_formatted',
        'first_last_name_formatted',
        'second_last_name_formatted',
        'pretty_age',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'death_date' => 'date',
        'age' => 'integer',
        'age_years' => 'integer',
        'age_months' => 'integer',
        'gov_folio' => 'string',
        // 'sex' no necesita cast - enum se maneja como string automáticamente
    ];

    /**
     * Accessor: pretty age string following the rule:
     * - if age_years >= 1 show "N años"
     * - else if age_months > 0 show "M meses"
     * - else fall back to age (if present) or null
     */
    public function getPrettyAgeAttribute()
    {
        $years = $this->age_years;
        $months = $this->age_months;

        if (!is_null($years) && $years >= 1) {
            return $years . ' años';
        }

        if (!is_null($months) && $months > 0) {
            return $months . ' meses';
        }

        if (!is_null($this->age) && $this->age > 0) {
            return $this->age . ' años';
        }

        return null;
    }

    /**
     * Relationship: Death belongs to Residence Municipality
     */
    public function residenceMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'residence_municipality_id');
    }

    /**
     * Relationship: Death belongs to Jurisdiction
     */
    public function jurisdiction()
    {
        return $this->belongsTo(Jurisdiction::class);
    }

    /**
     * Relationship: Death belongs to Death Location
     */
    public function deathLocation()
    {
        return $this->belongsTo(DeathLocation::class, 'death_location_id');
    }

    /**
     * Relationship: Death belongs to Death Cause
     */
    public function deathCause()
    {
        return $this->belongsTo(DeathCause::class, 'death_cause_id');
    }

    /**
     * Relationship: Death belongs to Death Municipality
     */
    public function deathMunicipality()
    {
        return $this->belongsTo(Municipality::class, 'death_municipality_id');
    }

    /**
     * Accessor: formatted full name (Title Case, preserves small words like 'de', 'la' in lower case).
     */
    public function getNameFormattedAttribute()
    {
        return self::formatPersonName($this->attributes['name'] ?? null);
    }

    /**
     * Accessor: formatted first last name
     */
    public function getFirstLastNameFormattedAttribute()
    {
        return self::formatPersonName($this->attributes['first_last_name'] ?? null);
    }

    /**
     * Accessor: formatted second last name
     */
    public function getSecondLastNameFormattedAttribute()
    {
        return self::formatPersonName($this->attributes['second_last_name'] ?? null);
    }

    /**
     * Helper to format person names: converts to Title Case and lowercases small words.
     * Made public so it can be reused by commands/scripts that normalize existing data.
     */
    public static function formatPersonName(?string $value): ?string
    {
        if ($value === null) return null;
        $s = trim($value);
        if ($s === '') return null;
        // Lowercase the whole string first to normalize ALL CAPS inputs
        $s = mb_strtolower($s, 'UTF-8');
        // Convert to title case (multibyte)
        $s = mb_convert_case($s, MB_CASE_TITLE, 'UTF-8');

        // Keep certain small words lowercase in Spanish (de, del, la, las, los, y, e, al, a)
        $small = ["De", "Del", "La", "Las", "Los", "Y", "E", "Al", "A", "El"];
        foreach ($small as $word) {
            // word boundaries, case-sensitive replacement (we know words are Title Case now)
            $s = preg_replace('/\b' . preg_quote($word, '/') . '\b/u', mb_strtolower($word, 'UTF-8'), $s);
        }

        return $s;
    }

    /**
     * Mutator: normalize name when saving so database becomes uniform.
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value === null ? null : self::formatPersonName($value);
    }

    public function setFirstLastNameAttribute($value)
    {
        $this->attributes['first_last_name'] = $value === null ? null : self::formatPersonName($value);
    }

    public function setSecondLastNameAttribute($value)
    {
        $this->attributes['second_last_name'] = $value === null ? null : self::formatPersonName($value);
    }
}
