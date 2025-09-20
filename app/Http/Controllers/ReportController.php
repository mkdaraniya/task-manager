<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TimeLog;
use App\Models\User;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReportController extends Controller
{
    // use AuthorizesRequests;

    public function __construct()
    {
        // $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        $users = User::all();
        $projects = Project::all();
        $statuses = Status::all();
        return view('reports.index', compact('users', 'projects', 'statuses'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:task-progress,user-activity,ticket-status',
            'format' => 'required|in:pdf,excel',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'user' => 'nullable|exists:users,id',
            'project' => 'nullable|exists:projects,id',
            'status' => 'nullable|exists:statuses,id',
        ]);

        $data = $this->getReportData($request->type, $request);

        if ($data->isEmpty()) {
            return back()->with('error', 'No data available for the selected criteria.');
        }

        $reportName = str_replace('-', '_', $request->type) . '_' . now()->format('Y_m_d_H_i_s');

        if ($request->format === 'excel') {
            return Excel::download(new \App\Exports\ReportExport($data), "{$reportName}.xlsx");
        }

        $type = $request->type; // Define $type
        $pdf = Pdf::loadView('reports.pdf', compact('data', 'type'));
        return $pdf->download("{$reportName}.pdf");
    }

    private function getReportData($type, $request)
    {
        $query = Task::with(['assignees', 'status', 'board.project']);

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->user) {
            $query->whereHas('assignees', fn($q) => $q->where('id', $request->user));
        }
        if ($request->project) {
            $query->whereHas('board.project', fn($q) => $q->where('id', $request->project));
        }
        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        switch ($type) {
            case 'task-progress':
                return $query->get()->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'status' => $task->status->name,
                        'assignees' => $task->assignees->pluck('name')->implode(', '),
                        'project' => $task->board->project->name ?? 'N/A',
                        'created_at' => $task->created_at->format('Y-m-d'),
                    ];
                })->groupBy('status');

            case 'user-activity':
                $query = TimeLog::with(['task', 'user'])
                    ->whereHas('task', fn($q) => $q->whereHas('assignees', fn($q2) => $q2->where('id', Auth::id())));
                if ($request->date_from) {
                    $query->whereDate('created_at', '>=', $request->date_from);
                }
                if ($request->date_to) {
                    $query->whereDate('created_at', '<=', $request->date_to);
                }
                return $query->get()->map(function ($log) {
                    return [
                        'user' => $log->user->name,
                        'task' => $log->task->title,
                        'duration' => $log->duration,
                        'created_at' => $log->created_at->format('Y-m-d H:i'),
                    ];
                })->groupBy('user');

            case 'ticket-status':
                return $query->get()->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'priority' => $task->priority,
                        'status' => $task->status->name,
                        'created_at' => $task->created_at->format('Y-m-d'),
                    ];
                })->groupBy('priority');

            default:
                return collect([]);
        }
    }
}
