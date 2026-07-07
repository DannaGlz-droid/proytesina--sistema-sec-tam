<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class District extends Model
{
    public const OTHER_NAME = 'OTRO';
    public const CENTRAL_OFFICE_NAME = 'Oficina Central';
    public const CENTRAL_OFFICE_ID = 999;

    private const ROMAN_DISTRICT_ORDER = [
        'I' => 1,
        'II' => 2,
        'III' => 3,
        'IV' => 4,
        'V' => 5,
        'VI' => 6,
        'VII' => 7,
        'VIII' => 8,
        'IX' => 9,
        'X' => 10,
        'XI' => 11,
        'XII' => 12,
    ];

     /**
     * The table associated with the model.
     */
    protected $table = 'districts';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name'
    ];

    public static function numberedQuery(): Builder
    {
        return static::query()->where(function (Builder $query) {
            foreach (array_keys(self::ROMAN_DISTRICT_ORDER) as $roman) {
                $query->orWhere('name', 'like', $roman . ' -%')
                    ->orWhere('name', 'like', $roman . '-%');
            }
        });
    }

    public static function numberedCatalog(): Collection
    {
        return static::numberedQuery()
            ->get()
            ->sortBy(fn (District $district) => self::sortOrder($district->name))
            ->values();
    }

    public static function statisticsCatalog(): Collection
    {
        $districts = static::numberedCatalog();
        $districts->push(static::firstOrCreate(['name' => self::OTHER_NAME]));

        return $districts;
    }

    public static function userAssignmentCatalog(): Collection
    {
        $districts = static::numberedCatalog();

        $centralOffice = new static();
        $centralOffice->id = self::CENTRAL_OFFICE_ID;
        $centralOffice->name = self::CENTRAL_OFFICE_NAME;
        $districts->push($centralOffice);

        return $districts;
    }

    public static function sortOrder(?string $name): int
    {
        if (!$name) {
            return 999;
        }

        $prefix = strtoupper(trim(strtok($name, '-')));

        return self::ROMAN_DISTRICT_ORDER[$prefix] ?? match (trim($name)) {
            self::OTHER_NAME => 98,
            self::CENTRAL_OFFICE_NAME => 99,
            default => 999,
        };
    }

    /**
     * Relationship: District has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class, 'district_id');
    }

    /**
     * Relationship: District has many Municipalities
     */
    public function municipalities()
    {
        return $this->hasMany(Municipality::class, 'district_id');
    }

    /**
     * Relationship: District has many Deaths
     */
    public function deaths()
    {
        return $this->hasMany(Death::class, 'district_id');
    }

    /**
     * Relationship: District has many RoadSafetyReports
     */
    public function roadSafetyReports()
    {
        return $this->hasMany(RoadSafetyReport::class, 'district_id');
    }

    /**
     * Relationship: District has many InjuryObservatoryReports
     */
    public function injuryObservatoryReports()
    {
        return $this->hasMany(InjuryObservatoryReport::class, 'district_id');
    }

    /**
     * Relationship: District has many GruposVulnerablesReports
     */
    public function gruposVulnerablesReports()
    {
        return $this->hasMany(GruposVulnerablesReport::class, 'district_id');
    }
}
