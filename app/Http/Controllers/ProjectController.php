<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Auth::user()->projects()->latest()->get();
        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,archived,pending',
        ]);

        $data['slug']       = Str::slug($data['name']);
        $data['status']     = $data['status'] ?? 'active';
        $data['start_date'] = $data['start_date'] ?? now();
        $data['end_date']   = $data['end_date'] ?? null;

        $project = Auth::user()->projects()->create($data);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully.',
                'data' => $project
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function update(Request $request, Project $project)
    {
        \Log::info('Update method called', [
            'project_id' => $project->id,
            'request_data' => $request->all(),
            'is_ajax' => $request->ajax()
        ]);

        $this->authorizeAccess($project);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|in:active,archived,pending',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $project->update($data);

        // Return JSON response for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully.',
                'data' => $project->fresh()
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        \Log::info('Destroy method called', [
            'project_id' => $project->id,
            'is_ajax' => request()->ajax()
        ]);

        $this->authorizeAccess($project);
        $project->update(['status' => 'archived']);

        // Return JSON response for AJAX requests
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Project archived successfully.'
            ]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project archived.');
    }

    public function show(Project $project)
    {
        $this->authorizeAccess($project);
        return view('projects.show', compact('project'));
    }

    private function authorizeAccess(Project $project)
    {
        if ($project->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }

    public function scopeActive($query)
    {
        $table = $this->getTable();

        if (Schema::hasColumn($table, 'is_active')) {
            return $query->where($table . '.is_active', true);
        }

        if (Schema::hasColumn($table, 'archived')) {
            return $query->where($table . '.archived', false);
        }

        if (Schema::hasColumn($table, 'status')) {
            return $query->where($table . '.status', 'active');
        }

        return $query;
    }
}
