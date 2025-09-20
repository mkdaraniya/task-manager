{{-- resources/views/boards/kanban.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="kanban-container">
        {{-- Header Section --}}
        <div class="kanban-header bg-white rounded-3 shadow-sm p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <h3 class="mb-0 fw-bold">{{ $board->name }}</h3>
                        <span class="badge bg-primary ms-3">{{ $board->project->name ?? 'No Project' }}</span>
                    </div>
                    <p class="text-muted mb-0 mt-2">
                        {{ $board->description ?? 'Manage your tasks efficiently with drag and drop' }}</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary" onclick="KanbanBoard.createTask()">
                        <i class="bi bi-plus-circle me-2"></i>New Task
                    </button>
                    <button class="btn btn-outline-secondary" onclick="KanbanBoard.createColumn()">
                        <i class="bi bi-columns-gap me-2"></i>Add Column
                    </button>
                    <button class="btn btn-outline-secondary" onclick="KanbanBoard.toggleFilters()">
                        <i class="bi bi-funnel me-2"></i>Filters
                    </button>
                </div>
            </div>

            {{-- Filter Section --}}
            <div id="filterSection" class="filter-section mt-4" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchFilter" placeholder="Search tasks...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Priority</label>
                        <select class="form-select" id="priorityFilter">
                            <option value="">All</option>
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Assignee</label>
                        <select class="form-select" id="assigneeFilter">
                            <option value="">All</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tags</label>
                        <select class="form-select" id="tagFilter">
                            <option value="">All</option>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Due Date</label>
                        <select class="form-select" id="dueDateFilter">
                            <option value="">All</option>
                            <option value="overdue">Overdue</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-sm btn-secondary w-100" onclick="KanbanBoard.clearFilters()">Clear</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanban Board --}}
        <div class="kanban-board-wrapper">
            <div class="kanban-board" id="kanbanBoard">
                @foreach ($board->statuses as $status)
                    <div class="kanban-column" data-status-id="{{ $status->id }}">
                        <div class="kanban-column-header"
                            style="background: {{ $status->color }}20; border-left: 4px solid {{ $status->color }};">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <h5 class="mb-0 fw-semibold">{{ $status->name }}</h5>
                                    <span class="task-count badge bg-secondary ms-2">0</span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#"
                                                onclick="KanbanBoard.editColumn({{ $status->id }})">Edit Column</a></li>
                                        <li><a class="dropdown-item" href="#"
                                                onclick="KanbanBoard.setWIPLimit({{ $status->id }})">Set WIP Limit</a>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item text-danger" href="#"
                                                onclick="KanbanBoard.deleteColumn({{ $status->id }})">Delete Column</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="wip-limit text-muted small mt-1"
                                @if (!$status->wip_limit) style="display: none;" @endif>
                                WIP Limit: <span class="wip-current">0</span>/<span
                                    class="wip-max">{{ $status->wip_limit ?? 5 }}</span>
                            </div>
                        </div>
                        <div class="kanban-column-body" data-status="{{ $status->id }}"
                            @if ($status->wip_limit) data-wip-limit="{{ $status->wip_limit }}" @endif>
                            {{-- Tasks will be loaded here --}}
                        </div>
                        <div class="kanban-column-footer">
                            <button class="btn btn-sm btn-light w-100"
                                onclick="KanbanBoard.quickAddTask({{ $status->id }})">
                                <i class="bi bi-plus me-1"></i>Add Task
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Task Details Modal --}}
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <div class="d-flex align-items-center">
                        <h5 class="modal-title fw-bold" id="taskTitle">Task Details</h5>
                        <span class="ms-3 badge" id="taskPriorityBadge"></span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            {{-- Task Main Content --}}
                            <div class="task-main-content">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Description</label>
                                    <div class="task-description-view" id="taskDescriptionView"></div>
                                    <textarea class="form-control task-description-edit" id="taskDescriptionEdit" rows="5" style="display: none;"></textarea>
                                    <button class="btn btn-sm btn-outline-primary mt-2" id="editDescriptionBtn"
                                        onclick="KanbanBoard.toggleDescriptionEdit()">
                                        <i class="bi bi-pencil me-1"></i>Edit Description
                                    </button>
                                </div>

                                {{-- Subtasks --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0">
                                            <i class="bi bi-list-check me-2"></i>Subtasks
                                            <span class="badge bg-secondary ms-2" id="subtaskProgress">0/0</span>
                                        </h6>
                                        <button class="btn btn-sm btn-outline-primary" onclick="KanbanBoard.addSubtask()">
                                            <i class="bi bi-plus me-1"></i>Add
                                        </button>
                                    </div>
                                    <div class="progress mb-3" style="height: 6px;">
                                        <div class="progress-bar bg-success" id="subtaskProgressBar" style="width: 0%">
                                        </div>
                                    </div>
                                    <div id="subtasksList"></div>
                                </div>

                                {{-- Attachments --}}
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-semibold mb-0">
                                            <i class="bi bi-paperclip me-2"></i>Attachments
                                        </h6>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="document.getElementById('fileUpload').click()">
                                            <i class="bi bi-upload me-1"></i>Upload
                                        </button>
                                        <input type="file" id="fileUpload" multiple style="display: none;"
                                            onchange="KanbanBoard.uploadFiles(this.files)">
                                    </div>
                                    <div id="attachmentsList" class="row g-2"></div>
                                </div>

                                {{-- Comments Section --}}
                                <div class="comments-section">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-chat-dots me-2"></i>Comments & Activity
                                    </h6>
                                    <div class="comment-input mb-3">
                                        <div class="d-flex">
                                            <img src="{{ Auth::user()->profile->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                                class="rounded-circle me-3" style="width: 40px; height: 40px;">
                                            <div class="flex-grow-1">
                                                <div class="comment-editor">
                                                    <textarea class="form-control" id="commentInput" placeholder="Write a comment..." rows="3"></textarea>
                                                    <div class="comment-toolbar mt-2">
                                                        <button class="btn btn-sm btn-light"
                                                            onclick="KanbanBoard.insertMention()">
                                                            <i class="bi bi-at"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-light"
                                                            onclick="document.getElementById('commentFileUpload').click()">
                                                            <i class="bi bi-paperclip"></i>
                                                        </button>
                                                        <input type="file" id="commentFileUpload" multiple
                                                            style="display: none;"
                                                            onchange="KanbanBoard.attachToComment(this.files)">
                                                        <button class="btn btn-sm btn-primary float-end"
                                                            onclick="KanbanBoard.postComment()">
                                                            Post Comment
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="commentsList"></div>
                                </div>

                                {{-- Add after Comments Section in Task Details Modal --}}
                                <div class="activity-log-section mt-4">
                                    <h6 class="fw-semibold mb-3">
                                        <i class="bi bi-clock-history me-2"></i>Activity Log
                                    </h6>
                                    <div id="activityLogList"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            {{-- Task Sidebar --}}
                            <div class="task-sidebar">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select" id="taskStatus">
                                        @foreach ($board->statuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Priority</label>
                                    <select class="form-select" id="taskPriority">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Assignees</label>
                                    <select class="form-select" id="taskAssignees" multiple>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Tags</label>
                                    <select class="form-select" id="taskTags" multiple>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Due Date</label>
                                    <input type="date" class="form-control" id="taskDueDate">
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Time Tracking</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="timeSpent" placeholder="Hours"
                                            step="0.5">
                                        <button class="btn btn-outline-secondary" onclick="KanbanBoard.logTime()">Log
                                            Time</button>
                                    </div>
                                    <small class="text-muted">Total: <span id="totalTimeLogged">0</span> hours</small>
                                </div>

                                <hr>

                                <div class="task-metadata text-muted small">
                                    <p class="mb-1">Created by: <span id="taskCreator"></span></p>
                                    <p class="mb-1">Created: <span id="taskCreatedAt"></span></p>
                                    <p class="mb-1">Updated: <span id="taskUpdatedAt"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" onclick="KanbanBoard.deleteTask()">Delete Task</button>
                    <button type="button" class="btn btn-primary" onclick="KanbanBoard.saveTaskDetails()">Save
                        Changes</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Create/Edit Task Modal --}}
    <div class="modal fade" id="taskFormModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="taskForm">
                    @csrf
                    <input type="hidden" id="taskId" name="id">
                    <input type="hidden" id="statusId" name="status_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" class="form-select">
                                        <option value="low">Low</option>
                                        <option value="medium" selected>Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input type="date" name="due_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assignees</label>
                            <select name="assignees[]" class="form-select" multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <select name="tags[]" class="form-select" multiple>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Column Management Modal --}}
    <div class="modal fade" id="columnModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="columnForm">
                    @csrf
                    <input type="hidden" id="columnId" name="id">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Column</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Column Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Color</label>
                            <input type="color" name="color" class="form-control form-control-color"
                                value="#6c757d">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">WIP Limit (optional)</label>
                            <input type="number" name="wip_limit" class="form-control" min="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Column</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        /* Kanban Board Styles */
        .kanban-container {
            padding: 20px;
        }

        .kanban-board-wrapper {
            overflow-x: auto;
            padding-bottom: 20px;
        }

        .kanban-board {
            display: flex;
            gap: 20px;
            min-width: fit-content;
            padding: 10px;
        }

        .kanban-column {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            min-width: 320px;
            max-width: 320px;
            display: flex;
            flex-direction: column;
        }

        .kanban-column-header {
            padding: 15px;
            border-radius: 12px 12px 0 0;
        }

        .kanban-column-body {
            flex: 1;
            padding: 10px;
            min-height: 400px;
            max-height: calc(100vh - 400px);
            overflow-y: auto;
        }

        .kanban-column-body.drag-over {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
        }

        .kanban-column-footer {
            padding: 10px;
            border-top: 1px solid #e9ecef;
        }

        /* Task Card Styles */
        .task-card {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: move;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease;
        }

        .task-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .task-card.dragging {
            opacity: 0.5;
            transform: rotate(3deg);
        }

        .task-card .task-title {
            font-weight: 600;
            margin-bottom: 8px;
            cursor: pointer;
        }

        .task-card .task-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .task-card .task-assignees {
            display: flex;
            margin-left: auto;
        }

        .task-card .task-assignee {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid white;
            margin-left: -8px;
        }

        .task-card .priority-indicator {
            width: 4px;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 8px 0 0 8px;
        }

        .priority-critical {
            background: #dc3545;
        }

        .priority-high {
            background: #fd7e14;
        }

        .priority-medium {
            background: #ffc107;
        }

        .priority-low {
            background: #28a745;
        }

        /* Comment Styles */
        .comment-item {
            padding: 15px;
            border-radius: 8px;
            background: #f8f9fa;
            margin-bottom: 10px;
        }

        .comment-item .comment-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .comment-item .comment-body {
            padding-left: 48px;
        }

        .comment-reply {
            margin-left: 48px;
            padding: 10px;
            background: white;
            border-left: 3px solid #e9ecef;
            margin-top: 10px;
        }

        /* Filter Section */
        .filter-section {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        /* Subtask Styles */
        .subtask-item {
            padding: 8px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .subtask-item.completed {
            opacity: 0.6;
            text-decoration: line-through;
        }

        /* Attachment Styles */
        .attachment-item {
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .attachment-item:hover {
            background: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .kanban-column {
                min-width: 280px;
            }
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        const KanbanBoard = {
            boardId: {{ $board->id }},
            currentTask: null,
            filters: {},
            sortableInstances: [],

            init() {
                this.loadTasks();
                this.initializeDragDrop();
                this.bindEvents();
                this.initializeFilters();
            },

            loadTasks() {
                $.get(`/api/boards/${this.boardId}/tasks`, this.filters)
                    .done(data => {
                        this.renderTasks(data.tasks);
                        this.updateColumnCounts();
                    })
                    .fail(xhr => {
                        Swal.fire('Error', 'Failed to load tasks', 'error');
                    });
            },

            renderTasks(tasks) {
                // Clear all columns
                $('.kanban-column-body').empty();

                tasks.forEach(task => {
                    const card = this.createTaskCard(task);
                    $(`.kanban-column-body[data-status="${task.status_id}"]`).append(card);
                });
            },

            createTaskCard(task) {
                const priorityClass = `priority-${task.priority}`;
                const dueClass = task.due_date && new Date(task.due_date) < new Date() ? 'text-danger' : '';

                const assignees = task.assignees ? task.assignees.map(a =>
                    `<img src="${a.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(a.name)}" class="task-assignee" title="${a.name}" data-bs-toggle="tooltip">`
                ).join('') : '';

                const tags = task.tags ? task.tags.map(t =>
                    `<span class="badge" style="background: ${t.color};">${t.name}</span>`
                ).join(' ') : '';

                return `
                <div class="task-card position-relative" data-task-id="${task.id}">
                    <div class="${priorityClass} priority-indicator"></div>
                    <div class="task-title" onclick="KanbanBoard.showTaskDetails(${task.id})">
                        ${task.title}
                    </div>
                    ${task.description ? `<p class="text-muted small mb-2">${task.description.substring(0, 100)}...</p>` : ''}
                    <div class="task-meta">
                        ${tags}
                        ${task.due_date ? `<small class="${dueClass}"><i class="bi bi-calendar"></i> ${new Date(task.due_date).toLocaleDateString()}</small>` : ''}
                        <div class="task-assignees">${assignees}</div>
                    </div>
                    <div class="task-stats mt-2 text-muted small">
                        ${task.subtasks_count ? `<span><i class="bi bi-check2-square"></i> ${task.completed_subtasks}/${task.subtasks_count}</span>` : ''}
                        ${task.comments_count ? `<span class="ms-2"><i class="bi bi-chat"></i> ${task.comments_count}</span>` : ''}
                        ${task.attachments_count ? `<span class="ms-2"><i class="bi bi-paperclip"></i> ${task.attachments_count}</span>` : ''}
                    </div>
                </div>
            `;
            },

            initializeDragDrop() {
                document.querySelectorAll('.kanban-column-body').forEach(column => {
                    const sortable = Sortable.create(column, {
                        group: 'kanban',
                        animation: 150,
                        ghostClass: 'dragging',
                        dragClass: 'dragging',
                        onStart: evt => {
                            evt.item.classList.add('dragging');
                        },
                        onEnd: evt => {
                            evt.item.classList.remove('dragging');

                            const taskId = evt.item.dataset.taskId;
                            const newStatusId = evt.to.dataset.status;
                            const newIndex = evt.newIndex;

                            this.updateTaskStatus(taskId, newStatusId, newIndex);
                        },
                        onMove: evt => {
                            const column = evt.to;
                            const wipLimit = column.dataset.wipLimit;
                            if (wipLimit) {
                                const currentCount = column.children.length;
                                if (currentCount >= parseInt(wipLimit)) {
                                    Swal.fire('WIP Limit', 'This column has reached its WIP limit',
                                        'warning');
                                    return false;
                                }
                            }
                        }
                    });
                    this.sortableInstances.push(sortable);
                });
            },

            updateTaskStatus(taskId, statusId, position) {
                $.ajax({
                        url: `/boards/${this.boardId}/tasks/${taskId}`,
                        method: 'PUT',
                        data: {
                            status_id: statusId,
                            order: position,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done(() => {
                        this.updateColumnCounts();
                        this.showNotification('Task moved successfully');
                    })
                    .fail(() => {
                        Swal.fire('Error', 'Failed to update task status', 'error');
                        this.loadTasks(); // Reload to restore original state
                    });
            },

            createTask(statusId = null) {
                $('#taskFormModal').modal('show');
                $('#taskForm')[0].reset();
                $('#taskId').val('');
                $('#statusId').val(statusId || '');
                $('.modal-title').text('Create New Task');
            },

            quickAddTask(statusId) {
                Swal.fire({
                    title: 'Quick Add Task',
                    input: 'text',
                    inputPlaceholder: 'Enter task title...',
                    showCancelButton: true,
                    confirmButtonText: 'Create',
                    preConfirm: (value) => {
                        if (!value || value.trim().length < 1) {
                            Swal.showValidationMessage('Please enter a title');
                        }
                        return value;
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        const title = result.value.trim();
                        $.ajax({
                                url: `/boards/${this.boardId}/tasks`,
                                method: 'POST',
                                data: {
                                    title,
                                    status_id: statusId || '',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType: 'json'
                            })
                            .done((res) => {
                                if (res.success) {
                                    const card = this.createTaskCard(res.data);
                                    $(`.kanban-column-body[data-status="${res.data.status_id}"]`).prepend(
                                        card);
                                    this.updateColumnCounts();
                                    this.showNotification('Task created');
                                } else {
                                    Swal.fire('Error', res.message || 'Failed to create task', 'error');
                                }
                            })
                            .fail((xhr) => {
                                Swal.fire('Error', 'Failed to create task', 'error');
                            });
                    }
                });
            },

            // Show task details in modal
            showTaskDetails(taskId) {
                $.get(`/api/tasks/${taskId}`)
                    .done((task) => {
                        this.currentTask = task;
                        // populate modal header & sidebar
                        $('#taskTitle').text(task.title || 'Task Details');
                        $('#taskPriorityBadge').attr('class', 'ms-3 badge').addClass(
                            `bg-${this._priorityBadgeColor(task.priority)}`).text(task.priority ? task.priority
                            .toUpperCase() : '');
                        $('#taskDescriptionView').html(task.description || '<em>No description</em>');
                        $('#taskDescriptionEdit').val(task.description || '');
                        $('#taskStatus').val(task.status ? task.status.id : '');
                        $('#taskPriority').val(task.priority || 'medium');
                        $('#taskAssignees').val((task.assignees || []).map(a => a.id)).trigger('change');
                        $('#taskTags').val((task.tags || []).map(t => t.id)).trigger('change');
                        $('#taskDueDate').val(task.due_date ? task.due_date.split('T')[0] : '');
                        $('#taskCreator').text(task.creator ? task.creator.name : (task.created_by || 'Unknown'));
                        $('#taskCreatedAt').text(task.created_at ? new Date(task.created_at).toLocaleString() :
                            '-');
                        $('#taskUpdatedAt').text(task.updated_at ? new Date(task.updated_at).toLocaleString() :
                            '-');
                        $('#totalTimeLogged').text((task.timeLogs || []).reduce((s, l) => s + parseFloat(l.hours ||
                            0), 0));

                        // subtasks
                        this._renderSubtasks(task.subtasks || []);

                        // attachments
                        this._renderAttachments(task.attachments || []);

                        // comments
                        this._renderComments(task.comments || []);

                        // Render activity logs
                        this._renderActivityLogs(task.activity_logs || []);

                        // show modal
                        const modalEl = document.querySelector('#taskDetailsModal');
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.show();
                    })
                    .fail(() => {
                        Swal.fire('Error', 'Failed to load task', 'error');
                    });
            },

            // helper to pick badge color class
            _priorityBadgeColor(priority) {
                if (priority === 'critical') return 'danger';
                if (priority === 'high') return 'warning';
                if (priority === 'medium') return 'info';
                return 'success';
            },

            _renderActivityLogs(logs) {
                const $logList = $('#activityLogList').empty();
                (logs || []).forEach(log => {
                    $logList.append(`
            <div class="activity-item mb-2">
                <div class="d-flex align-items-center">
                    <strong>${log.causer?.name || 'System'}</strong>
                    <small class="text-muted ms-2">${new Date(log.created_at).toLocaleString()}</small>
                </div>
                <div>${log.description}</div>
                ${log.properties ? `<small class="text-muted">${JSON.stringify(log.properties, null, 2)}</small>` : ''}
            </div>
        `);
                });
            },

            // Save details from the sidebar
            saveTaskDetails() {
                if (!this.currentTask || !this.currentTask.id) return;
                const id = this.currentTask.id;
                const payload = {
                    title: $('#taskTitle').text(),
                    description: $('#taskDescriptionEdit').val(),
                    priority: $('#taskPriority').val(),
                    status_id: $('#taskStatus').val(),
                    due_date: $('#taskDueDate').val(),
                    assignees: $('#taskAssignees').val() || [],
                    tags: $('#taskTags').val() || [],
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PUT'
                };

                $.ajax({
                        url: `/boards/${this.boardId}/tasks/${id}`,
                        method: 'POST', // Laravel-friendly; _method=PUT will be respected
                        data: payload,
                        dataType: 'json'
                    })
                    .done((res) => {
                        if (res.success) {
                            // update card in DOM (replace or update)
                            const updated = res.data;
                            const cardHtml = this.createTaskCard(updated);
                            const $col = $(`.kanban-column-body[data-status="${updated.status_id}"]`);
                            // Try to replace existing card, otherwise re-load column
                            const old = $(`.task-card[data-task-id="${updated.id}"]`);
                            if (old.length) {
                                old.replaceWith(cardHtml);
                            } else {
                                $col.prepend(cardHtml);
                            }
                            this.updateColumnCounts();
                            this.showNotification('Task updated');
                        }
                    })
                    .fail(() => {
                        Swal.fire('Error', 'Failed to save task', 'error');
                    });
            },

            // Delete task (from details modal)
            deleteTask() {
                if (!this.currentTask || !this.currentTask.id) return;
                const id = this.currentTask.id;
                Swal.fire({
                    title: 'Delete task?',
                    text: 'This will archive/delete the task.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete'
                }).then((r) => {
                    if (!r.isConfirmed) return;
                    $.ajax({
                            url: `/boards/${this.boardId}/tasks/${id}`,
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            dataType: 'json'
                        })
                        .done((res) => {
                            $('#taskDetailsModal').modal('hide');
                            $(`.task-card[data-task-id="${id}"]`).fadeOut(200, function() {
                                $(this).remove();
                            });
                            this.updateColumnCounts();
                            this.showNotification('Task deleted');
                        })
                        .fail(() => {
                            Swal.fire('Error', 'Failed to delete task', 'error');
                        });
                });
            },

            // Subtasks: add new subtask for current task
            addSubtask() {
                if (!this.currentTask || !this.currentTask.id) return;
                Swal.fire({
                    title: 'New Subtask',
                    input: 'text',
                    inputPlaceholder: 'Subtask title',
                    showCancelButton: true,
                    confirmButtonText: 'Add'
                }).then((r) => {
                    if (!r.isConfirmed || !r.value) return;
                    $.post(`/tasks/${this.currentTask.id}/subtasks`, {
                            title: r.value,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done((subtask) => {
                            // refresh subtasks list
                            this.currentTask.subtasks = this.currentTask.subtasks || [];
                            this.currentTask.subtasks.push(subtask);
                            this._renderSubtasks(this.currentTask.subtasks);
                            this.showNotification('Subtask added');
                        })
                        .fail(() => Swal.fire('Error', 'Failed to add subtask', 'error'));
                });
            },

            // helper to render subtasks area
            _renderSubtasks(list) {
                const $list = $('#subtasksList').empty();
                const completed = list.filter(s => s.completed).length;
                $('#subtaskProgress').text(`${completed}/${list.length}`);
                const percent = list.length ? Math.round((completed / list.length) * 100) : 0;
                $('#subtaskProgressBar').css('width', `${percent}%`);

                list.forEach(st => {
                    const cls = st.completed ? 'subtask-item completed' : 'subtask-item';
                    const checked = st.completed ? 'checked' : '';
                    const el = $(`
                            <div class="${cls}" data-subtask-id="${st.id}">
                                <input type="checkbox" class="me-2 subtask-toggle" ${checked}>
                                <div class="flex-grow-1">${st.title}</div>
                            </div>
                        `);
                    $list.append(el);
                });

                // toggle handler (delegated)
                $list.off('change', '.subtask-toggle').on('change', '.subtask-toggle', (e) => {
                    const subtaskId = $(e.target).closest('.subtask-item').data('subtask-id');
                    $.ajax({
                            url: `/subtasks/${subtaskId}`,
                            method: 'PATCH',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        })
                        .done((sub) => {
                            // update local state
                            const idx = (this.currentTask.subtasks || []).findIndex(s => s.id == sub.id);
                            if (idx !== -1) this.currentTask.subtasks[idx] = sub;
                            this._renderSubtasks(this.currentTask.subtasks || []);
                        })
                        .fail(() => Swal.fire('Error', 'Failed to toggle subtask', 'error'));
                });
            },

            // Toggle description edit view
            toggleDescriptionEdit() {
                const view = $('#taskDescriptionView');
                const edit = $('#taskDescriptionEdit');
                if (edit.is(':visible')) {
                    // save to view (don't persist to server until saveTaskDetails)
                    view.html(edit.val() || '<em>No description</em>');
                    edit.hide();
                    view.show();
                } else {
                    edit.show();
                    view.hide();
                }
            },

            // Attachments upload (task details context)
            uploadFiles(files) {
                if (!this.currentTask || !this.currentTask.id) return;
                const fd = new FormData();
                for (let i = 0; i < files.length; i++) fd.append('file[]', files[i]);
                // endpoint expects attachable id then type; adapts to your controller signature
                $.ajax({
                        url: `/attachments/${this.currentTask.id}/tasks`,
                        method: 'POST',
                        data: fd,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    })
                    .done((attachments) => {
                        // append and re-render attachments list
                        this.currentTask.attachments = this.currentTask.attachments || [];
                        // if API returns array or single object handle both
                        if (Array.isArray(attachments)) this.currentTask.attachments.push(...attachments);
                        else this.currentTask.attachments.push(attachments);
                        this._renderAttachments(this.currentTask.attachments);
                        this.showNotification('Files uploaded');
                    })
                    .fail(() => Swal.fire('Error', 'Upload failed', 'error'));
            },

            _renderAttachments(list) {
                const $box = $('#attachmentsList').empty();
                (list || []).forEach(att => {
                    // att.path expected; adapt if your API returns full url
                    const url = att.path ? (att.url || `/storage/${att.path}`) : '#';
                    $box.append(`
                            <div class="col-4 col-md-3">
                                <div class="attachment-item" data-id="${att.id}">
                                    <a href="${url}" target="_blank">${att.original_name || 'Attachment'}</a>
                                </div>
                            </div>
                        `);
                });
            },

            // Attach files to comment (store files temporarily and add to comment payload)
            attachToComment(files) {
                // simple approach: upload files and add them to comment when posted
                // for brevity, call uploadFiles (shares attachments on task)
                this.uploadFiles(files);
            },

            // Post a comment for current task
            postComment() {
                if (!this.currentTask || !this.currentTask.id) return;
                const body = $('#commentInput').val();
                if (!body || body.trim() === '') return Swal.fire('Error', 'Comment cannot be empty', 'error');

                $.post(`/tasks/${this.currentTask.id}/comments`, {
                        body,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    })
                    .done((comment) => {
                        this.currentTask.comments = this.currentTask.comments || [];
                        this.currentTask.comments.unshift(comment);
                        this._renderComments(this.currentTask.comments);
                        $('#commentInput').val('');
                        this.showNotification('Comment posted');
                    })
                    .fail(() => Swal.fire('Error', 'Failed to post comment', 'error'));
            },

            _renderComments(list) {
                const $c = $('#commentsList').empty();
                (list || []).forEach(cm => {
                    $c.append(`
                            <div class="comment-item">
                                <div class="comment-header">
                                    <strong>${cm.user?.name || 'User'}</strong> <small class="text-muted ms-2">${new Date(cm.created_at).toLocaleString()}</small>
                                </div>
                                <div class="comment-body">${cm.body}</div>
                            </div>
                        `);
                });
            },

            insertMention() {
                // very simple: insert @ and focus the input
                const input = document.getElementById('commentInput');
                input.value = input.value + ' @';
                input.focus();
            },

            logTime() {
                if (!this.currentTask || !this.currentTask.id) return;
                const hours = $('#timeSpent').val();
                if (!hours || isNaN(hours) || hours <= 0) return Swal.fire('Error', 'Enter valid hours', 'error');

                $.post(`/tasks/${this.currentTask.id}/time-log`, {
                    hours,
                    _token: $('meta[name="csrf-token"]').attr('content')
                }).done((log) => {
                    this.currentTask.timeLogs = this.currentTask.timeLogs || [];
                    this.currentTask.timeLogs.push(log);
                    const total = this.currentTask.timeLogs.reduce((s, l) => s + parseFloat(l.hours), 0);
                    $('#totalTimeLogged').text(total);
                    $('#timeSpent').val('');
                    this.showNotification('Time logged');
                }).fail(() => Swal.fire('Error', 'Failed to log time', 'error'));
            },

            // Filters related helpers
            initializeFilters() {
                $('#searchFilter, #priorityFilter, #assigneeFilter, #tagFilter, #dueDateFilter').on('change keyup',
                    () => {
                        this.filters = {
                            search: $('#searchFilter').val(),
                            priority: $('#priorityFilter').val(),
                            user: $('#assigneeFilter').val(),
                            tag: $('#tagFilter').val(),
                            due: $('#dueDateFilter').val()
                        };
                        // debounce small
                        clearTimeout(this._filterTimer);
                        this._filterTimer = setTimeout(() => this.loadTasks(), 300);
                    });
            },

            toggleFilters() {
                $('#filterSection').toggle();
            },

            clearFilters() {
                $('#searchFilter').val('');
                $('#priorityFilter').val('');
                $('#assigneeFilter').val('');
                $('#tagFilter').val('');
                $('#dueDateFilter').val('');
                this.filters = {};
                this.loadTasks();
            },

            // update the badge numbers on each column
            updateColumnCounts() {
                $('.kanban-column').each((i, col) => {
                    const $col = $(col);
                    const statusId = $col.find('.kanban-column-body').data('status');
                    const count = $col.find('.kanban-column-body').children('.task-card').length;
                    $col.find('.task-count').text(count);
                    // update WIP current if present
                    $col.find('.wip-current').text(count);
                });
            },

            // small notifier wrapper
            showNotification(text) {
                if (typeof Swal !== 'undefined') {
                    // non-blocking toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        icon: 'success',
                        title: text
                    });
                } else {
                    console.log(text);
                }
            },

            // Bind misc events (tooltips etc.)
            bindEvents() {
                // initialize bootstrap tooltips on dynamic content
                $(document).on('mouseenter', '[data-bs-toggle="tooltip"]', function() {
                    const $this = $(this);
                    if (!$this.data('bs.tooltip')) {
                        new bootstrap.Tooltip(this);
                    }
                });
            },

            createColumn() {
                $('#columnModal').modal('show');
                $('#columnForm')[0].reset();
                $('#columnId').val('');
                $('.modal-title').text('Add Column');
            },

            editColumn(id) {
                // Assuming an API endpoint to fetch column details exists (e.g., in BoardController)
                $.get(`/boards/${this.boardId}/statuses/${id}`)
                    .done((data) => {
                        $('#columnId').val(data.id);
                        $('input[name="name"]').val(data.name);
                        $('input[name="color"]').val(data.color || '#6c757d');
                        $('input[name="wip_limit"]').val(data.wip_limit);
                        $('.modal-title').text('Edit Column');
                        $('#columnModal').modal('show');
                    })
                    .fail(() => Swal.fire('Error', 'Failed to load column', 'error'));
            },

            setWIPLimit(id) {
                const $col = $(`.kanban-column[data-status-id="${id}"]`);
                const currentLimit = $col.find('.kanban-column-body').data('wipLimit') || 0;
                Swal.fire({
                    title: 'Set WIP Limit',
                    input: 'number',
                    inputValue: currentLimit,
                    inputAttributes: {
                        min: 0
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Set'
                }).then((r) => {
                    if (r.isConfirmed) {
                        const wip = parseInt(r.value) || 0;
                        $.ajax({
                            url: `/boards/${this.boardId}/statuses/${id}`,
                            method: 'POST',
                            data: {
                                wip_limit: wip,
                                _method: 'PATCH',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).done(() => {
                            const $wip = $col.find('.wip-limit');
                            $wip.find('.wip-max').text(wip || 5);
                            if (wip > 0) {
                                $wip.show();
                                $col.find('.kanban-column-body').attr('data-wip-limit', wip).data(
                                    'wip-limit', wip);
                            } else {
                                $wip.hide();
                                $col.find('.kanban-column-body').removeAttr('data-wip-limit')
                                    .removeData('wip-limit');
                            }
                            this.showNotification('WIP limit updated');
                        }).fail(() => Swal.fire('Error', 'Failed to update WIP limit', 'error'));
                    }
                });
            },

            deleteColumn(id) {
                Swal.fire({
                    title: 'Delete Column?',
                    text: 'This will delete the column and may affect tasks.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Delete'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/boards/${this.boardId}/statuses/${id}`,
                            method: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            }
                        }).done(() => {
                            $(`.kanban-column[data-status-id="${id}"]`).remove();
                            this.showNotification('Column deleted');
                        }).fail(() => Swal.fire('Error', 'Failed to delete column', 'error'));
                    }
                });
            }
        }; // end KanbanBoard object

        // initialize on DOM ready
        $(function() {
            KanbanBoard.init();

            // Task form submit handler
            $('#taskForm').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize();
                const taskId = $('#taskId').val();
                const url = taskId ? `/boards/${KanbanBoard.boardId}/tasks/${taskId}` :
                    `/boards/${KanbanBoard.boardId}/tasks`;
                if (taskId) data += '&_method=PUT';
                $.post(url, data)
                    .done((res) => {
                        if (res.success) {
                            const task = res.data;
                            const card = KanbanBoard.createTaskCard(task);
                            if (taskId) {
                                $(`.task-card[data-task-id="${taskId}"]`).replaceWith(card);
                            } else {
                                const status = task.status_id || $('.kanban-column-body').first().data(
                                    'status');
                                $(`.kanban-column-body[data-status="${status}"]`).prepend(card);
                            }
                            KanbanBoard.updateColumnCounts();
                            $('#taskFormModal').modal('hide');
                            KanbanBoard.showNotification('Task saved');
                        } else {
                            Swal.fire('Error', res.message || 'Failed to save task', 'error');
                        }
                    })
                    .fail(() => Swal.fire('Error', 'Failed to save task', 'error'));
            });

            // Column form submit handler (assuming routes exist for statuses)
            $('#columnForm').on('submit', function(e) {
                e.preventDefault();
                let data = $(this).serialize();
                const colId = $('#columnId').val();
                const url = colId ? `/boards/${KanbanBoard.boardId}/statuses/${colId}` :
                    `/boards/${KanbanBoard.boardId}/statuses`;
                if (colId) data += '&_method=PUT';
                $.post(url, data)
                    .done((res) => {
                        if (res.success) {
                            const status = res.data;
                            if (colId) {
                                // Update existing column
                                const $col = $(`.kanban-column[data-status-id="${colId}"]`);
                                $col.find('h5').text(status.name);
                                $col.find('.kanban-column-header').css({
                                    'background': `${status.color}20`,
                                    'border-left': `4px solid ${status.color}`
                                });
                                const $wip = $col.find('.wip-limit');
                                $wip.find('.wip-max').text(status.wip_limit || 5);
                                if (status.wip_limit) {
                                    $wip.show();
                                    $col.find('.kanban-column-body').attr('data-wip-limit', status
                                        .wip_limit).data('wip-limit', status.wip_limit);
                                } else {
                                    $wip.hide();
                                    $col.find('.kanban-column-body').removeAttr('data-wip-limit')
                                        .removeData('wip-limit');
                                }
                                KanbanBoard.showNotification('Column updated');
                            } else {
                                // Add new column
                                const columnHtml = `
                                    <div class="kanban-column" data-status-id="${status.id}">
                                        <div class="kanban-column-header" style="background: ${status.color}20; border-left: 4px solid ${status.color};">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <h5 class="mb-0 fw-semibold">${status.name}</h5>
                                                    <span class="task-count badge bg-secondary ms-2">0</span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown">
                                                        <i class="bi bi-three-dots-vertical"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="KanbanBoard.editColumn(${status.id})">Edit Column</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="KanbanBoard.setWIPLimit(${status.id})">Set WIP Limit</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="KanbanBoard.deleteColumn(${status.id})">Delete Column</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="wip-limit text-muted small mt-1" ${status.wip_limit ? '' : 'style="display: none;"'}>
                                                WIP Limit: <span class="wip-current">0</span>/<span class="wip-max">${status.wip_limit || 5}</span>
                                            </div>
                                        </div>
                                        <div class="kanban-column-body" data-status="${status.id}" ${status.wip_limit ? `data-wip-limit="${status.wip_limit}"` : ''}>
                                        </div>
                                        <div class="kanban-column-footer">
                                            <button class="btn btn-sm btn-light w-100" onclick="KanbanBoard.quickAddTask(${status.id})">
                                                <i class="bi bi-plus me-1"></i>Add Task
                                            </button>
                                        </div>
                                    </div>
                                `;
                                $('#kanbanBoard').append(columnHtml);
                                const newBody = document.querySelector(
                                    '.kanban-column-body:last-child');
                                const sortable = Sortable.create(newBody, {
                                    group: 'kanban',
                                    animation: 150,
                                    ghostClass: 'dragging',
                                    dragClass: 'dragging',
                                    onStart: evt => evt.item.classList.add('dragging'),
                                    onEnd: evt => {
                                        evt.item.classList.remove('dragging');
                                        const taskId = evt.item.dataset.taskId;
                                        const newStatusId = evt.to.dataset.status;
                                        const newIndex = evt.newIndex;
                                        KanbanBoard.updateTaskStatus(taskId, newStatusId,
                                            newIndex);
                                    },
                                    onMove: evt => {
                                        const column = evt.to;
                                        const wipLimit = column.dataset.wipLimit;
                                        if (wipLimit && column.children.length >= parseInt(
                                                wipLimit)) {
                                            Swal.fire('WIP Limit',
                                                'This column has reached its WIP limit',
                                                'warning');
                                            return false;
                                        }
                                    }
                                });
                                KanbanBoard.sortableInstances.push(sortable);
                                KanbanBoard.showNotification('Column created');
                            }
                            $('#columnModal').modal('hide');
                        } else {
                            Swal.fire('Error', res.message || 'Failed to save column', 'error');
                        }
                    })
                    .fail(() => Swal.fire('Error', 'Failed to save column', 'error'));
            });

        });
    </script>
@endpush
