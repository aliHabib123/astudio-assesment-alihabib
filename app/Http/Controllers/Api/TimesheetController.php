<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TimesheetController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = $request->user()->timesheets();

        // Filter by project if project_id is provided
        if ($request->has('project_id')) {
            $projectId = $request->input('project_id');

            // Verify user belongs to project
            $project = $request->user()->projects()->findOrFail($projectId);

            $query->where('project_id', $projectId);
        }

        $timesheets = $query->latest()->paginate();

        return response()->json($timesheets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'hours' => ['required', 'numeric', 'min:0.5', 'max:24'],
            'task_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        // Verify user belongs to project
        $project = $request->user()->projects()->findOrFail($validated['project_id']);

        return DB::transaction(function () use ($validated, $request) {
            // Create timesheet entry directly
            $timesheet = $request->user()->timesheets()->create([
                'project_id' => $validated['project_id'],
                'date' => $validated['date'],
                'hours' => $validated['hours'],
                'task_name' => $validated['task_name'],
                'description' => $validated['description']
            ]);

            return response()->json([
                'message' => 'Time entry added successfully',
                'data' => $timesheet
            ], 201);
        });
    }

    public function show(Request $request, $id)
    {
        try {
            $timesheet = Timesheet::findOrFail($id);
            $this->authorize('view', $timesheet);

            return response()->json([
                'data' => $timesheet
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Resource not found.',
                'details' => 'The requested record does not exist or has been deleted.'
            ], 404);
        }
    }

    public function update(Request $request, Timesheet $timesheet)
    {
        $this->authorize('update', $timesheet);

        $validated = $request->validate([
            'hours' => ['sometimes', 'numeric', 'min:0.5', 'max:24'],
            'task_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
        ]);

        $timesheet->update($validated);

        return response()->json([
            'message' => 'Time entry updated successfully',
            'data' => $timesheet
        ]);
    }

    public function destroy(Request $request, Timesheet $timesheet)
    {
        $this->authorize('delete', $timesheet);

        $timesheet->delete();

        return response()->json([
            'message' => 'Time entry deleted successfully'
        ]);
    }
}
