<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Attribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Get validation rules for an attribute based on its type
     */
    protected function getAttributeValidationRule(Attribute $attribute): string|array
    {
        return match($attribute->type) {
            'text' => 'string',
            'number' => 'numeric',
            'date' => 'date',
            'select' => ['string', Rule::in($attribute->options ?? [])],
            default => 'string',
        };
    }

    /**
     * Validate attribute values based on their types
     */
    protected function validateAttributeValues(array $attributeValues): array
    {
        $errors = [];
        $validatedValues = [];

        foreach ($attributeValues as $index => $attributeValue) {
            if (!isset($attributeValue['key']) || !isset($attributeValue['value'])) {
                $errors["attribute_values.{$index}"] = ['Missing key or value'];
                continue;
            }

            $attribute = Attribute::where('key', $attributeValue['key'])->first();
            if (!$attribute) {
                $errors["attribute_values.{$index}.key"] = ['Invalid attribute key'];
                continue;
            }

            $rule = $this->getAttributeValidationRule($attribute);
            $validator = Validator::make(
                ['value' => $attributeValue['value']],
                ['value' => $rule]
            );

            if ($validator->fails()) {
                $errors["attribute_values.{$index}.value"] = $validator->errors()->get('value');
                continue;
            }

            // Cast the value to the appropriate type
            $value = match($attribute->type) {
                'number' => (float) $attributeValue['value'],
                'date' => date('Y-m-d', strtotime($attributeValue['value'])),
                default => $attributeValue['value'],
            };

            $validatedValues[] = [
                'key' => $attributeValue['key'],
                'value' => $value,
                'attribute' => $attribute,
            ];
        }

        return ['errors' => $errors, 'validated' => $validatedValues];
    }

    /**
     * Display a listing of the projects.
     */
    public function index(): AnonymousResourceCollection
    {
        $projects = Auth::user()->projects()->paginate();
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|string|exists:project_statuses,slug',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'attribute_values' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate attribute values
        if ($request->has('attribute_values')) {
            $result = $this->validateAttributeValues($request->attribute_values);
            if (!empty($result['errors'])) {
                return response()->json(['errors' => $result['errors']], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Get status_id from slug
            $status = ProjectStatus::where('slug', $request->status)->firstOrFail();
            
            $project = Project::create([
                'name' => $request->name,
                'status_id' => $status->id,
            ]);
            
            // Assign users to project
            if ($request->has('user_ids')) {
                $project->users()->sync($request->user_ids);
            }

            // Add current user as project member if not already added
            if (!$project->users->contains(Auth::id())) {
                $project->users()->attach(Auth::id());
            }

            // Store attribute values using keys
            if ($request->has('attribute_values')) {
                foreach ($result['validated'] as $item) {
                    $project->attributeValues()->create([
                        'attribute_id' => $item['attribute']->id,
                        'value' => $item['value'],
                    ]);
                }
            }

            DB::commit();

            $project->load(['users', 'attributeValues.attribute', 'status']);
            return response()->json(new ProjectResource($project), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error creating project', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): JsonResponse
    {
        if (!$project->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized to view this project'], 403);
        }

        $project->load(['users', 'attributeValues.attribute', 'status']);
        return response()->json(new ProjectResource($project));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        if (!$project->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized to update this project'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|exists:project_statuses,slug',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'attribute_values' => 'array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate attribute values
        if ($request->has('attribute_values')) {
            $result = $this->validateAttributeValues($request->attribute_values);
            if (!empty($result['errors'])) {
                return response()->json(['errors' => $result['errors']], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Update basic info
            if ($request->has('name')) {
                $project->name = $request->name;
            }

            // Update status using slug
            if ($request->has('status')) {
                $status = ProjectStatus::where('slug', $request->status)->firstOrFail();
                $project->status_id = $status->id;
            }

            $project->save();

            // Update users
            if ($request->has('user_ids')) {
                $project->users()->sync($request->user_ids);
                // Ensure current user remains in the project
                if (!$project->users->contains(Auth::id())) {
                    $project->users()->attach(Auth::id());
                }
            }

            // Update attribute values using keys
            if ($request->has('attribute_values')) {
                foreach ($result['validated'] as $item) {
                    $project->attributeValues()->updateOrCreate(
                        ['attribute_id' => $item['attribute']->id],
                        ['value' => $item['value']]
                    );
                }
            }

            DB::commit();

            $project->load(['users', 'attributeValues.attribute', 'status']);
            return response()->json(new ProjectResource($project));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error updating project', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project): JsonResponse
    {
        if (!$project->users->contains(Auth::id())) {
            return response()->json(['message' => 'Unauthorized to delete this project'], 403);
        }

        $project->delete();
        return response()->json([
            'message' => 'Project deleted successfully',
            'data' => new ProjectResource($project)
        ]);
    }
}
