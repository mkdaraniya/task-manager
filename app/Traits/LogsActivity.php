<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected function logActivity($action, $description, $model = null, $properties = [])
    {
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'properties' => !empty($properties) ? $properties : null,
        ]);

        // Broadcast for real-time dashboard updates
        broadcast(new \App\Events\DashboardUpdated(
            $this->getDashboardStats(),
            $this->getRecentActivities()
        ))->toOthers();

        return $log;
    }

    protected function getDashboardStats()
    {
        $user = Auth::user();
        return [
            'tasks' => $user->tasks()->where('archived', false)->count(),
            'completed_tasks' => $user->tasks()->where('completed_at', '!=', null)->count(),
            'pending_tasks' => $user->tasks()->whereNull('completed_at')->count(),
            'projects' => $user->projects()->where('status', 'active')->count(),
            'team_members' => $user->teams()->withCount('users')->get()->sum('users_count'),
        ];
    }

    protected function getRecentActivities()
    {
        return ActivityLog::whereIn('user_id', Auth::user()->teams()->with('users')->get()->pluck('users.*.id')->flatten())
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
    }
}
