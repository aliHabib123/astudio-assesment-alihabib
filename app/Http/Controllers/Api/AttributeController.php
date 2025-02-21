<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttributeResource;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    /**
     * Display a listing of the attributes.
     */
    public function index(): AnonymousResourceCollection
    {
        $attributes = Attribute::paginate();
        return AttributeResource::collection($attributes);
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:attributes',
            'type' => ['required', 'string', Rule::in(['text', 'date', 'number', 'select'])],
            'options' => 'nullable|array',
            'default_value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attribute = Attribute::create($validator->validated());
        return response()->json(new AttributeResource($attribute), 201);
    }

    /**
     * Display the specified attribute.
     */
    public function show(Attribute $attribute): AttributeResource
    {
        return new AttributeResource($attribute);
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, Attribute $attribute): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'key' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('attributes')->ignore($attribute->id)],
            'type' => ['sometimes', 'required', 'string', Rule::in(['text', 'date', 'number', 'select'])],
            'options' => 'nullable|array',
            'default_value' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $attribute->update($validator->validated());
        return response()->json(new AttributeResource($attribute));
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(Attribute $attribute): JsonResponse
    {
        $attribute->delete();
        return response()->json([
            'message' => 'Attribute deleted successfully',
            'data' => new AttributeResource($attribute)
        ], 200);
    }
}
