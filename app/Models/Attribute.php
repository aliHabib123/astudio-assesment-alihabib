<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'key',
        'type',
        'options',
        'default_value',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the values for this attribute.
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class);
    }

    /**
     * Cast value based on attribute type.
     */
    public function castValue($value)
    {
        if (empty($value)) {
            return null;
        }

        return match($this->type) {
            'date' => \Carbon\Carbon::parse($value)->format('Y-m-d'),
            'number' => (float) $value,
            default => (string) $value,
        };
    }

    /**
     * Validate value based on attribute type and options.
     */
    public function validateValue($value): bool
    {
        if (empty($value)) {
            return true;
        }

        return match($this->type) {
            'date' => strtotime($value) !== false,
            'number' => is_numeric($value),
            'select' => in_array($value, $this->options ?? []),
            default => true,
        };
    }
}
