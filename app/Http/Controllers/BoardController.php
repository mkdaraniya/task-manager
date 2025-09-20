<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Board;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    use LogsActivity;

    public function kanban(Board $board)
    {
        // Ensure statuses are loaded and ordered by position if you have that column
        $board->load(['statuses' => function ($q) {
            $q->orderBy('position');
        }, 'project']);

        // Provide lists for filters / modals
        $users = User::orderBy('name')->get();
        $tags  = Tag::orderBy('name')->get();

        return view('boards.kanban', compact('board', 'users', 'tags'));
    }

    public function index(Project $project)
    {
        $boards = $project->boards;
        return view('boards.index', compact('project', 'boards'));
    }

    public function create(Project $project)
    {
        return view('boards.create', compact('project'));
    }

    public function store(Request $request, Project $project)
    {
        $request->validate(['name' => 'required']);
        $board = $project->boards()->create($request->all());
        // Create default statuses
        $defaultStatuses = [
            ['name' => 'To Do', 'color' => '#007bff', 'position' => 1],
            ['name' => 'In Progress', 'color' => '#ffc107', 'position' => 2],
            ['name' => 'Done', 'color' => '#28a745', 'position' => 3],
        ];
        $board->statuses()->createMany($defaultStatuses);

        // Log the board creation
        $this->logActivity('created', "created board '{$board->name}'", $board);

        return redirect()->route('boards.show', [$project, $board]);
    }

    public function show(Project $project, Board $board)
    {
        $tasks = $board->tasks()->with(['assignees', 'tags', 'status'])->active()->get()->groupBy('status_id');
        $statuses = $board->statuses;
        return view('boards.show', compact('project', 'board', 'tasks', 'statuses'));
    }

    public function update(Request $request, Project $project, Board $board)
    {
        $request->validate(['name' => 'required']);
        $board->update($request->all());

        // Log the board update
        $this->logActivity('updated', "updated board '{$board->name}'", $board);

        return redirect()->route('boards.show', [$project, $board]);
    }

    public function destroy(Project $project, Board $board)
    {
        $boardName = $board->name; // Store name before deletion
        $board->delete();

        // Log the board deletion
        $this->logActivity('deleted', "deleted board '{$boardName}'", $board);

        return redirect()->route('boards.index', $project);
    }
}
