<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $statuses = Status::all();
        $users = User::all();
        return view('calendar.index', compact('statuses', 'users'));
    }

    public function events(Request $request)
    {
        $query = Task::whereNotNull('due_date')->with(['status', 'assignees']);

        // Filters
        if ($request->status_id) {
            $query->where('status_id', $request->status_id);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->assignee_id) {
            $query->whereHas('assignees', fn($q) => $q->where('id', $request->assignee_id));
        }
        if ($request->start && $request->end) {
            $query->whereBetween('due_date', [$request->start, $request->end]);
        }

        $events = $query->get()->map(function ($task) {
            $color = $task->status->color ?? '#007bff';
            return [
                'id' => $task->id,
                'title' => $task->title . ' (' . ucfirst($task->priority) . ')',
                'start' => $task->due_date->format('Y-m-d'),
                'end' => $task->due_date->format('Y-m-d'),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('calendar.task.show', $task),
                'extendedProps' => [
                    'description' => $task->description ?? 'No description',
                    'priority' => $task->priority,
                    'assignees' => $task->assignees->pluck('name')->implode(', '),
                ],
            ];
        });

        return response()->json($events);
    }

    public function showTask($id)
    {
        $task = Task::with(['status', 'assignees', 'board'])->findOrFail($id);
        return response()->json($task);
    }
}
