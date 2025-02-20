<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'attribute_id',
        'value',
    ];

    /**
     * Get the project that owns the attribute value.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the attribute that owns the value.
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class);
    }

    /**
     * Get the cast value based on the attribute type.
     */
    public function getCastValue()
    {
        return $this->attribute->castValue($this->value);
    }

    /**
     * Set the value with validation.
     */
    public function setValue($value): void
    {
        if (!$this->attribute->validateValue($value)) {
            throw new \InvalidArgumentException("Invalid value for attribute {$this->attribute->name}");
        }

        $this->value = $value;
    }
}
