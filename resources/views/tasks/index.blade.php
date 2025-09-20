@extends('layouts.app')

@section('content')
    @php
        // determine a valid board if any
        $kanbanBoard = $board ?? ($tasks->first()?->board ?? null);
    @endphp

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Tasks</h3>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">+ New Task</button>

                @if ($kanbanBoard)
                    <a href="{{ route('boards.kanban', $kanbanBoard) }}" class="btn btn-outline-secondary">Open Kanban</a>
                @endif
            </div>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Assignees</th>
                    <th>Priority</th>
                    <th>Due</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="taskTableBody">
                @foreach ($tasks as $task)
                    <tr id="row-{{ $task->id }}">
                        <td>{{ $task->id }}</td>
                        <td class="title">{{ $task->title }}</td>
                        <td class="assignees">
                            @foreach ($task->assignees as $u)
                                <span class="badge bg-secondary">{{ $u->name }}</span>
                            @endforeach
                        </td>
                        <td class="priority">{{ ucfirst($task->priority) }}</td>
                        <td class="due">{{ $task->due_date?->format('Y-m-d') ?? '-' }}</td>
                        <td class="status">{{ $task->status?->name ?? '-' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $task->id }}"
                                data-title="{{ htmlspecialchars($task->title, ENT_QUOTES) }}"
                                data-description="{{ htmlspecialchars($task->description ?? '', ENT_QUOTES) }}"
                                data-priority="{{ $task->priority }}"
                                data-due="{{ $task->due_date?->format('Y-m-d') ?? '' }}"
                                data-assignees="{{ $task->assignees->pluck('id')->implode(',') }}">
                                Edit
                            </button>

                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $task->id }}">
                                Delete
                            </button>
                            <a href="{{ route('boards.tasks.show', [$task->board_id, $task]) }}"
                                class="btn btn-sm btn-info">Open</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- Create Task Modal --}}
    <div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="createTaskForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">New Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Board selection (nullable for global tasks) --}}
                        <div class="mb-3">
                            <label class="form-label">Board</label>
                            <select name="board_id" class="form-select">
                                <option value="">-- No Board / Global --</option>
                                @foreach ($boards as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}
                                        ({{ $b->project?->name ?? 'No Project' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assignees</label>
                            <select name="assignees[]" class="form-select" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <select name="tags[]" class="form-select" multiple>
                                @foreach ($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Initial Comment</label>
                            <textarea name="comment" class="form-control" rows="3" placeholder="Optional comment"></textarea>
                        </div>
                        <div class="activity-log-section mt-4">
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-clock-history me-2"></i>Activity Log
                            </h6>
                            <div id="activityLogList"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" type="submit">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Task Modal --}}
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="editTaskForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTaskId" name="id">

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" id="editTaskTitle" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="editTaskDescription" name="description" class="form-control" rows="4"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select id="editTaskPriority" name="priority" class="form-select">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" id="editTaskDueDate" name="due_date" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assignees</label>
                            <select id="editTaskAssignees" name="assignees[]" class="form-select" multiple>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <select id="editTaskTags" name="tags[]" class="form-select" multiple>
                                @foreach ($tags as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                        <button class="btn btn-primary" type="submit">Update</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // AJAX CSRF
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Create
            $('#createTaskForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const btn = form.find('button[type="submit"]').prop('disabled', true).text('Creating...');
                let url =
                    "{{ $board ? route('boards.tasks.store', $board) : route('tasks.store.global') }}";
                $.post(url, form.serialize())
                    .done(function(res) {
                        if (res.success) {
                            $('#taskTableBody').prepend(renderTaskRow(res.data));
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'createTaskModal'));
                            if (modal) modal.hide();
                            form[0].reset();
                            Swal.fire('Success', res.message, 'success');
                        } else {
                            Swal.fire('Error', res.message || 'Failed', 'error');
                        }
                    })
                    .fail(function(xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function() {
                        btn.prop('disabled', false).text('Create');
                    });
            });

            // Edit open
            $(document).on('click', '.edit-btn', function() {
                const btn = $(this);
                const id = btn.data('id');
                $('#editTaskId').val(id);
                $('#editTaskTitle').val(btn.attr('data-title'));
                $('#editTaskDescription').val(btn.attr('data-description') || '');
                $('#editTaskPriority').val(btn.attr('data-priority') || 'medium');
                $('#editTaskDueDate').val(btn.attr('data-due') || '');
                const assignees = (btn.attr('data-assignees') || '').split(',').filter(Boolean);
                $('#editTaskAssignees').val(assignees);
                const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                editModal.show();
            });

            // Update
            $('#editTaskForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const id = $('#editTaskId').val();
                const btn = form.find('button[type="submit"]').prop('disabled', true).text('Updating...');
                $.ajax({
                        url: "{{ url('tasks') }}/" + id,
                        method: 'POST',
                        data: form.serialize(), // includes _method=PUT
                        dataType: 'json'
                    })
                    .done(function(res) {
                        if (res.success) {
                            $('#row-' + res.id).replaceWith(renderTaskRow(res));
                            const modal = bootstrap.Modal.getInstance(document.getElementById(
                                'editTaskModal'));
                            if (modal) modal.hide();
                            Swal.fire('Success', 'Task updated', 'success');
                            // reload to ensure statuses/order are correct OR update DOM item
                            location.reload();
                        } else {
                            Swal.fire('Error', res.message || 'Failed to update', 'error');
                        }
                    })
                    .fail(function(xhr) {
                        handleAjaxError(xhr);
                    })
                    .always(function() {
                        btn.prop('disabled', false).text('Update');
                    });
            });

            // Delete
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: 'Delete task?',
                    text: 'This will archive the task.',
                    icon: 'warning',
                    showCancelButton: true
                }).then((r) => {
                    if (!r.isConfirmed) return;
                    $.post("{{ url('tasks') }}/" + id, {
                            _method: 'DELETE',
                            _token: $('meta[name="csrf-token"]').attr('content')
                        })
                        .done(function(res) {
                            $('#row-' + id).fadeOut(200, function() {
                                $(this).remove();
                            });
                            Swal.fire('Archived!', res.message || 'Task archived', 'success');
                        })
                        .fail(function(xhr) {
                            handleAjaxError(xhr);
                        });
                });
            });

            // helper render
            function renderTaskRow(task) {
                const assignees = (task.assignees || []).map(u =>
                    `<span class="badge bg-secondary">${u.name}</span>`).join(' ');
                const due = task.due_date ? task.due_date.split('T')[0] : '-';
                return `
            <tr id="row-${task.id}">
                <td>${task.id}</td>
                <td class="title">${task.title}</td>
                <td class="assignees">${assignees}</td>
                <td class="priority">${task.priority || ''}</td>
                <td class="due">${due}</td>
                <td class="status">${task.status?task.status.name:'-'}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning edit-btn"
                        data-id="${task.id}"
                        data-title="${escapeHtml(task.title)}"
                        data-description="${escapeHtml(task.description||'')}"
                        data-priority="${task.priority}"
                        data-due="${due}"
                        data-assignees="${(task.assignees||[]).map(u=>u.id).join(',')}">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${task.id}">Delete</button>
                    <a href="/boards/${task.board_id}/tasks/${task.id}" class="btn btn-sm btn-info">Open</a>
                </td>
            </tr>
        `;
            }

            // small escape helper
            function escapeHtml(s) {
                if (s === null || typeof s === 'undefined') return '';
                return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g,
                    '&lt;').replace(/>/g, '&gt;');
            }

            // central AJAX error handler
            function handleAjaxError(xhr) {
                console.error('AJAX Error', xhr);
                let message = 'An error occurred';
                if (xhr && xhr.responseJSON) {
                    if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                    else if (xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join(
                        ', ');
                } else if (xhr && xhr.status) {
                    message = 'Server error: ' + xhr.status;
                }
                Swal.fire('Error', message, 'error');
            }
        });

        showTaskDetails(taskId) {
                $.get(`/api/tasks/${taskId}`)
                    .done((task) => {
                        this.currentTask = task;
                        // Existing code for populating modal...
                        // ...

                        // Render activity logs
                        this._renderActivityLogs(task.activity_logs || []);

                        // Show modal
                        const modalEl = document.querySelector('#taskDetailsModal');
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        modal.show();
                    })
                    .fail(() => {
                        Swal.fire('Error', 'Failed to load task', 'error');
                    });
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
    </script>
@endpush
