<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\Task;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $user = Auth::user();

        // Fetch dynamic stats
        $stats = [
            'tasks' => $user->tasks()->where('archived', false)->count(),
            'completed_tasks' => $user->tasks()->whereNotNull('completed_at')->count(),
            'pending_tasks' => $user->tasks()->whereNull('completed_at')->count(),
            'projects' => $user->projects()->where('status', 'active')->count(),
            'team_members' => $user->teams()->withCount('users')->get()->sum('users_count'),
        ];

        // Fetch recent projects with progress and team
        $recentProjects = $user->projects()->where('status', 'active')->latest()->take(3)->get()->map(function ($project) {
            $totalTasks = $project->tasks()->count();
            $completedTasks = $project->tasks()->whereNotNull('completed_at')->count();
            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            // Fetch team members through teams associated with the project
            $teamUsers = $project->boards()->with('tasks.assignees')->get()
                ->pluck('tasks.*.assignees')->flatten()->unique('id')->take(2)
                ->map->only('id', 'name');
            $teamCount = $project->boards()->with('tasks.assignees')->get()
                ->pluck('tasks.*.assignees')->flatten()->unique('id')->count();

            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description ?? 'No description',
                'progress' => $progress,
                'team' => $teamUsers,
                'team_count' => $teamCount,
                'due_date' => $project->deadline ? $project->deadline->format('M d, Y') : 'N/A',
                'status' => ucfirst($project->status),
            ];
        });

        // Fetch recent activities
        $recentActivities = ActivityLog::whereIn('user_id', $user->teams()->with('users')->get()->pluck('users.*.id')->flatten())
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'user_name' => $activity->user->name,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });

        // Team performance metrics
        $teamPerformance = [
            'tasks_completed' => $stats['completed_tasks'] / max($stats['tasks'], 1) * 100,
            'project_delivery' => $user->projects()->where('status', 'active')->where('deadline', '>=', now())->count() / max($stats['projects'], 1) * 100,
            'team_satisfaction' => 78, // Placeholder; replace with actual metric
        ];

        // Chart data (tasks by status and project progress)
        $chartData = [
            'tasks_by_status' => [
                'labels' => ['Pending', 'Completed'],
                'data' => [$stats['pending_tasks'], $stats['completed_tasks']],
                'colors' => ['#ff9a9e', '#4facfe'],
            ],
            'project_progress' => $user->projects()->where('status', 'active')->latest()->take(5)->get()->map(function ($project) {
                $totalTasks = $project->tasks()->count();
                $completedTasks = $project->tasks()->whereNotNull('completed_at')->count();
                return [
                    'name' => $project->name,
                    'progress' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
                ];
            }),
        ];

        return view('dashboard', compact('stats', 'recentProjects', 'recentActivities', 'teamPerformance', 'chartData'));
    }

    public function liveData(Request $request)
    {
        $user = Auth::user();

        $stats = [
            'tasks' => $user->tasks()->where('archived', false)->count(),
            'completed_tasks' => $user->tasks()->whereNotNull('completed_at')->count(),
            'pending_tasks' => $user->tasks()->whereNull('completed_at')->count(),
            'projects' => $user->projects()->where('status', 'active')->count(),
            'team_members' => $user->teams()->withCount('users')->get()->sum('users_count'),
        ];

        $recentActivities = ActivityLog::whereIn('user_id', $user->teams()->with('users')->get()->pluck('users.*.id')->flatten())
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'user_name' => $activity->user->name,
                    'description' => $activity->description,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });

        $chartData = [
            'tasks_by_status' => [
                'labels' => ['Pending', 'Completed'],
                'data' => [$stats['pending_tasks'], $stats['completed_tasks']],
                'colors' => ['#ff9a9e', '#4facfe'],
            ],
            'project_progress' => $user->projects()->where('status', 'active')->latest()->take(5)->get()->map(function ($project) {
                $totalTasks = $project->tasks()->count();
                $completedTasks = $project->tasks()->whereNotNull('completed_at')->count();
                return [
                    'name' => $project->name,
                    'progress' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
                ];
            }),
        ];

        // Broadcast to Reverb channel
        broadcast(new \App\Events\DashboardUpdated($stats, $recentActivities, $chartData))->toOthers();

        return response()->json([
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
        ]);
    }
}
