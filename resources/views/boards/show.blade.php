@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2>{{ $board->name }} - Kanban Board</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taskModal">New Task</button>
    <div class="kanban-board row" id="kanban">
        @foreach($statuses as $status)
            <div class="col-md-3">
                <div class="card status-column" data-status-id="{{ $status->id }}">
                    <div class="card-header">
                        <h5>{{ $status->name }} <span class="badge" style="background: {{ $status->color }}">{{ $tasks->get($status->id)?->count() ?? 0 }}</span></h5>
                    </div>
                    <div class="card-body task-list" data-status="{{ $status->id }}">
                        @foreach($tasks->get($status->id, collect()) as $task)
                            <div class="card task-card mb-2" data-task-id="{{ $task->id }}" draggable="true">
                                <div class="card-body">
                                    <h6>{{ $task->title }}</h6>
                                    <p class="small">{{ Str::limit($task->description, 50) }}</p>
                                    <span class="badge bg-{{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                                    @if($task->due_date)
                                        <span class="badge bg-warning">{{ $task->due_date->format('M d') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="taskForm" method="POST" action="{{ route('boards.tasks.store', $board) }}">
                @csrf
                <div class="modal-header">
                    <h5>New Task</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="title" class="form-control" placeholder="Title" required>
                    <textarea name="description" class="form-control mt-2" placeholder="Description (Markdown)"></textarea>
                    <select name="priority" class="form-control mt-2">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                    <input type="date" name="due_date" class="form-control mt-2">
                    <!-- Assignees and tags selects -->
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // Drag & Drop Kanban
    const columns = document.querySelectorAll('.status-column');
    columns.forEach(column => {
        new Sortable(column.querySelector('.task-list'), {
            group: 'kanban',
            animation: 150,
            onEnd: function(evt) {
                const taskId = evt.item.dataset.taskId;
                const newStatusId = evt.to.dataset.status;
                fetch(`/api/tasks/${taskId}`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify({ status_id: newStatusId })
                }).then(() => {
                    // Update count badges via AJAX
                });
            }
        });
    });

    // AJAX task creation
    document.getElementById('taskForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        }).then(res => res.json()).then(data => {
            // Append new task card to column
            location.reload();  // Simple reload; optimize with DOM append
        });
    });

    // Search & filters
    // Implement with AJAX calls to /api/tasks/search
</script>
@endpush
@endsection
