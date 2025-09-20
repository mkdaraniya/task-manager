<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Board;
use App\Models\User;
use App\Models\Tag;
use App\Models\Subtask;
use App\Models\Comment;
use App\Models\Attachment;
use App\Models\Status;
use App\Models\TimeLog;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\CommonMarkConverter;
use Illuminate\Support\Str;


class TaskController extends Controller
{
    use LogsActivity;
    // inside TaskController

    public function index(Request $request, Board $board = null)
    {
        // If this request is for a specific board (route: boards/{board}/tasks)
        if ($board) {
            $query = $board->tasks()->where('archived', false);

            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('status')) {
                $query->where('status_id', $request->status);
            }
            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }
            if ($request->filled('tag')) {
                $query->whereHas('tags', fn($q) => $q->where('id', $request->tag));
            }
            if ($request->filled('user')) {
                $query->whereHas('assignees', fn($q) => $q->where('id', $request->user));
            }
            if ($request->filled('due')) {
                $today = now()->startOfDay();
                switch ($request->due) {
                    case 'overdue':
                        $query->where('due_date', '<', $today);
                        break;
                    case 'today':
                        $query->whereDate('due_date', $today);
                        break;
                    case 'week':
                        $query->whereBetween('due_date', [$today, $today->copy()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereBetween('due_date', [$today, $today->copy()->endOfMonth()]);
                        break;
                }
            }

            $tasks = $query->with(['assignees', 'tags', 'status'])->get();

            // return JSON for AJAX (kanban)
            if ($request->ajax()) {
                return response()->json(['tasks' => $tasks]);
            }

            $users = User::all();
            $tags  = Tag::all();
            $boards = Board::all();

            return view('tasks.index', compact('board', 'tasks', 'users', 'tags', 'boards'));
        }

        // --- No board provided: global tasks listing (/tasks) ---

        $query = Task::query()->where('archived', false);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn($q) => $q->where('id', $request->tag));
        }
        if ($request->filled('user')) {
            $query->whereHas('assignees', fn($q) => $q->where('id', $request->user));
        }
        if ($request->filled('due')) {
            $today = now()->startOfDay();
            switch ($request->due) {
                case 'overdue':
                    $query->where('due_date', '<', $today);
                    break;
                case 'today':
                    $query->whereDate('due_date', $today);
                    break;
                case 'week':
                    $query->whereBetween('due_date', [$today, $today->copy()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereBetween('due_date', [$today, $today->copy()->endOfMonth()]);
                    break;
            }
        }

        $tasks = $query->with(['assignees', 'tags', 'status', 'board'])->latest()->get();

        $users = User::all();
        $tags  = Tag::all();
        $boards = Board::all();

        // For non-board listing we pass null board to view — view must handle this (see next snippet)
        return view('tasks.index', compact('board', 'tasks', 'users', 'tags', 'boards'));
    }



    public function store(Request $request, Board $board)
    {
        // Validate request
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,critical',
            'due_date'    => 'nullable|date|after:today',
            'assignees'   => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'tags'        => 'nullable|array',
            'tags.*'      => 'exists:tags,id',
        ]);

        // Ensure board exists
        if (!$board) {
            return response()->json(['success' => false, 'message' => 'Board not found.'], 422);
        }

        // Get first status of board (default)
        $status = $board->statuses()->first();
        if (!$status) {
            return response()->json(['success' => false, 'message' => 'Board has no statuses.'], 422);
        }

        // Generate slug from title
        $slug = Str::slug($request->title);

        // Ensure slug uniqueness
        $originalSlug = $slug;
        $counter = 1;
        while (\App\Models\Task::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Prepare task data
        $data = $request->only(['title', 'description', 'priority', 'due_date']);
        $data['created_by'] = Auth::id();
        $data['status_id']  = $status->id;
        $data['slug']       = $slug;

        // Create task
        $task = $board->tasks()->create($data);

        // Enhanced logging with more context
        $this->logActivity('created_task', "Created task: {$task->title} on board: {$board->name}", $task, [
            'status' => $status->name,
            'assignees' => $request->filled('assignees') ? User::whereIn('id', $request->assignees)->pluck('name')->toArray() : [],
            'tags' => $request->filled('tags') ? Tag::whereIn('id', $request->tags)->pluck('name')->toArray() : [],
        ]);

        // Sync assignees if provided
        if ($request->filled('assignees')) {
            $task->assignees()->sync($request->assignees);
        }

        // Sync tags if provided
        if ($request->filled('tags')) {
            $task->tags()->sync($request->tags);
        }

        // Notify assignees safely
        if ($task->assignees()->count()) {
            try {
                $task->assignees->each->notify(new \App\Notifications\TaskAssigned($task));
            } catch (\Throwable $e) {
                \Log::warning('Task assigned notification failed: ' . $e->getMessage());
            }
        }

        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully.',
                'data'    => $task->load(['assignees', 'tags', 'status'])
            ]);
        }

        // Redirect for normal requests
        return redirect()->route('boards.show', [$board->project, $board])
            ->with('success', 'Task created successfully!');
    }



    public function create(Board $board)
    {
        $users = User::all();
        $tags = Tag::all();
        return view('tasks.create', compact('board', 'users', 'tags'));
    }

    public function show(Board $board, Task $task)
    {
        $task->load(['assignees', 'tags', 'subtasks', 'comments.user', 'attachments', 'timeLogs.user']);
        $users = User::all();
        $tags = Tag::all();
        return view('tasks.show', compact('task', 'users', 'tags'));
    }

    public function update(Request $request, Board $board, Task $task)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'due_date' => 'nullable|date',
            'assignees' => 'sometimes|array',
            'assignees.*' => 'exists:users,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'exists:tags,id',
            'status_id' => 'sometimes|exists:statuses,id',
            'order' => 'sometimes|integer',
        ]);

        $oldValues = $task->only(array_keys($validated));
        // Detect changes BEFORE saving
        $changed = [];
        foreach ($validated as $key => $value) {
            if ($task->{$key} != $value) {
                $changed[$key] = [
                    'old' => $task->{$key},
                    'new' => $value,
                ];
            }
        }

        $task->update($request->only(['title', 'description', 'priority', 'due_date', 'status_id', 'order']));
        if ($request->has('assignees')) {
            $oldAssignees = $task->assignees()->pluck('name')->toArray();
            $task->assignees()->sync($request->assignees);
            $newAssignees = User::whereIn('id', $request->assignees ?? [])->pluck('name')->toArray();
            if ($oldAssignees != $newAssignees) {
                $this->logActivity('updated_task_assignees', "Updated assignees for task: {$task->title}", $task, [
                    'old_assignees' => $oldAssignees,
                    'new_assignees' => $newAssignees,
                ]);
            }
        }

        if ($request->has('tags')) {
            $oldTags = $task->tags()->pluck('name')->toArray();
            $task->tags()->sync($request->tags);
            $newTags = Tag::whereIn('id', $request->tags ?? [])->pluck('name')->toArray();
            if ($oldTags != $newTags) {
                $this->logActivity('updated_task_tags', "Updated tags for task: {$task->title}", $task, [
                    'old_tags' => $oldTags,
                    'new_tags' => $newTags,
                ]);
            }
        }

        // Log field-specific changes
        if (isset($changed['status_id'])) {
            $this->logActivity('changed_task_status', "Changed task status for: {$task->title}", $task, [
                'old_status' => Status::find($changed['status_id']['old'])->name ?? 'N/A',
                'new_status' => Status::find($changed['status_id']['new'])->name ?? 'N/A',
            ]);
        }

        if (isset($changed['board_id'])) {
            $this->logActivity('moved_task_board', "Moved task to new board: {$task->title}", $task, [
                'old_board' => Board::find($changed['board_id']['old'])->name ?? 'N/A',
                'new_board' => Board::find($changed['board_id']['new'])->name ?? 'N/A',
            ]);
        }

        if (!empty($changed)) {
            $this->logActivity('updated_task', "Updated task: {$task->title}", $task, [
                'changes' => $changed,
            ]);
        }

        return response()->json(['success' => true, 'data' => $task->load(['assignees', 'tags', 'status'])]);
    }

    public function destroy(Board $board, Task $task)
    {
        $title = $task->title;
        $task->update(['archived' => true]);
        $this->logActivity('deleted_task', "Deleted task: {$title}", $task, [
            'board' => $board->name,
        ]);
        return response()->json(['success' => true]);
    }

    // Subtask methods
    public function addSubtask(Request $request, Task $task)
    {
        $subtask = $task->subtasks()->create($request->only('title'));
        $this->logActivity('created_subtask', "Added subtask: {$subtask->title} to task: {$task->title}", $subtask, [
            'task_id' => $task->id,
            'board' => $task->board->name,
        ]);
        return response()->json($subtask);
    }

    public function toggleSubtask(Request $request, Subtask $subtask)
    {
        $subtask->update(['completed' => !$subtask->completed]);
        $this->logActivity('toggled_subtask', "Toggled subtask: {$subtask->title} to " . ($subtask->completed ? 'completed' : 'incomplete'), $subtask, [
            'task' => $subtask->task->title,
            'task_id' => $subtask->task_id,
            'board' => $subtask->task->board->name,
        ]);
        return response()->json($subtask);
    }

    // Comment methods
    public function addComment(Request $request, Task $task)
    {
        $request->validate(['body' => 'required']);
        $comment = $task->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        // Notify mentioned users
        preg_match_all('/@([^\s]+)/', $request->body, $matches);
        $mentioned = User::whereIn('name', $matches[1])->get();
        $mentionedNames = $mentioned->pluck('name')->toArray();
        try {
            $mentioned->each->notify(new \App\Notifications\MentionedInComment($comment));
        } catch (\Throwable $e) {
            \Log::warning('Mention notification failed: ' . $e->getMessage());
        }
        $this->logActivity('added_comment', "Added comment to task: {$task->title}", $comment, [
            'mentions' => $mentionedNames,
            'board' => $task->board->name,
        ]);
        return response()->json($comment->load('user'));
    }

    // Attachment upload
    public function uploadAttachment(Request $request, $attachableId, $type = 'tasks')
    {
        $request->validate([
            'file' => 'required|array',
            'file.*' => 'file|max:10240'
        ]);
        $attachments = [];
        $attachable = $type === 'tasks' ? Task::find($attachableId) : Comment::find($attachableId);
        foreach ($request->file('file') as $file) {
            $path = $file->store("attachments/{$type}", 'public');
            $attachment = $attachable->attachments()->create([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'user_id' => Auth::id(),
            ]);
            $attachments[] = $attachment;
            $attachableName = isset($attachable->title) ? $attachable->title : (isset($attachable->body) ? $attachable->body : '');
            $this->logActivity('uploaded_attachment', "Uploaded attachment: {$attachment->original_name} to {$type}: {$attachableName}", $attachment, [
                'attachable_type' => $type,
                'attachable_id' => $attachableId,
                'board' => $type === 'tasks' ? $attachable->board->name : $attachable->task->board->name,
            ]);
        }
        return response()->json($attachments);
    }

    // Time log
    public function logTime(Request $request, Task $task)
    {
        $validated = $request->validate(['hours' => 'required|numeric|min:0', 'description' => 'nullable']);
        $log = $task->timeLogs()->create(array_merge($request->only(['hours', 'description']), ['user_id' => Auth::id()]));
        $this->logActivity('logged_time', "Logged {$validated['hours']} hours on task: {$task->title}", $log, [
            'task_id' => $task->id,
            'board' => $task->board->name,
            'description' => $validated['description'] ?? null,
        ]);
        return response()->json($log);
    }

    // Markdown converter (utility)
    public static function markdown($text)
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        return $converter->convertToHtml($text);
    }

    // Add inside TaskController (near other methods)
    public function apiShow(Task $task)
    {
        $task->load([
            'assignees:id,name',
            'tags:id,name,color',
            'subtasks',
            'comments.user',
            'attachments',
            'timeLogs.user',
            'status'
        ]);

        $task->completed_subtasks = $task->subtasks()->where('completed', true)->count();

        // Normalize assignees for front-end (avatar fallback)
        $task->assignees = $task->assignees->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'avatar' => $u->profile_picture ?? ($u->avatar ?? null)
            ];
        });

        return response()->json($task);
    }

    public function scopeActive($query)
    {
        $table = $this->getTable();

        // prefer an explicit archived boolean column
        if (Schema::hasColumn($table, 'archived')) {
            return $query->where($table . '.archived', false);
        }

        // fallback to is_active boolean column (some tables use this)
        if (Schema::hasColumn($table, 'is_active')) {
            return $query->where($table . '.is_active', true);
        }

        // fallback to a status string column (e.g. 'active', 'archived')
        if (Schema::hasColumn($table, 'status')) {
            return $query->where($table . '.status', 'active');
        }

        // if none of the common columns exist, return unmodified query
        return $query;
    }

    public function storeGlobal(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
            'due_date' => 'nullable|date|after:now',
            'assignees' => 'array',
            'tags' => 'array',
        ]);

        // Use provided board or default
        $board = $request->board_id ? Board::find($request->board_id) : $this->getDefaultBoard();

        return $this->store($request, $board);
    }


    protected function getDefaultBoard(): Board
    {
        // Ensure board exists with slug
        $board = Board::firstOrCreate(
            ['name' => 'My Tasks', 'project_id' => null],
            ['slug' => Str::slug('My Tasks')]
        );

        // Ensure at least one default status exists
        if (!$board->statuses()->exists()) {
            $defaultStatuses = [
                ['name' => 'To Do', 'position' => 1],
                ['name' => 'In Progress', 'position' => 2],
                ['name' => 'Done', 'position' => 3],
            ];

            // Add slug for each status
            foreach ($defaultStatuses as &$status) {
                $slug = Str::slug($status['name']);
                $original = $slug;
                $counter = 1;

                // Ensure slug is unique within this board
                while ($board->statuses()->where('slug', $slug)->exists()) {
                    $slug = $original . '-' . $counter;
                    $counter++;
                }

                $status['slug'] = $slug;
            }

            $board->statuses()->createMany($defaultStatuses);
        }

        return $board;
    }
}
