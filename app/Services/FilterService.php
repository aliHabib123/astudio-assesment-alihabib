<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FilterService
{
    protected $operators = [
        'eq' => '=',
        'gt' => '>',
        'lt' => '<',
        'gte' => '>=',
        'lte' => '<=',
        'like' => 'LIKE',
        'not' => '!=',
    ];

    public function apply(Builder $query, array $filters, array $allowedFields = [], array $allowedEav = [])
    {
        foreach ($filters as $field => $value) {
            // Parse operator from field name (e.g., name:like => ['name', 'like'])
            $parts = explode(':', $field);
            $fieldName = $parts[0];
            $operator = $parts[1] ?? 'eq';

            // Get the actual operator symbol
            $operatorSymbol = $this->operators[$operator] ?? '=';

            // Handle LIKE operator
            if ($operatorSymbol === 'LIKE') {
                $value = '%' . $value . '%';
            }

            // Apply the filter
            if (in_array($fieldName, $allowedEav)) {
                $this->applyEavFilter($query, $fieldName, $operatorSymbol, $value);
            } elseif (in_array($fieldName, $allowedFields)) {
                $query->where($fieldName, $operatorSymbol, $value);
            } else {
                // Removed logging statement, but kept the else condition
            }
        }

        return $query;
    }

    protected function applyEavFilter(Builder $query, string $attributeKey, string $operator, $value)
    {
        // Removed logging statement
        return $query->whereExists(function ($subquery) use ($attributeKey, $operator, $value) {
            $subquery->select(DB::raw(1))
                    ->from('attribute_values')
                    ->join('attributes', 'attributes.id', '=', 'attribute_values.attribute_id')
                    ->where('attributes.key', '=', $attributeKey)
                    ->where('attribute_values.value', $operator, $value)
                    ->whereRaw('attribute_values.project_id = projects.id');
        });
    }
}
