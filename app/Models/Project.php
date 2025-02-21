<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Timesheet;
use App\Models\AttributeValue;
use App\Models\ProjectStatus;
use App\Services\FilterService;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_id' => 'integer',
    ];

    // Define which fields can be filtered
    protected static $allowedFilters = [
        'name',
        'status_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the allowed EAV attributes for filtering dynamically
     */
    public static function getAllowedEavFilters()
    {
        return \App\Models\Attribute::pluck('key')->toArray();
    }

    /**
     * Get the users assigned to the project.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    /**
     * Get the timesheets for the project.
     */
    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    /**
     * Get the attribute values for the project.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Get the status of the project.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function scopeFilter(Builder $query, array $filters)
    {
        return (new FilterService())->apply(
            $query,
            $filters,
            static::$allowedFilters,
            static::getAllowedEavFilters()
        );
    }
}
