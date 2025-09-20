@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label for="status_id" class="form-label">Status</label>
                            <select id="status_id" name="status_id" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select id="priority" name="priority" class="form-select">
                                <option value="">All Priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="assignee_id" class="form-label">Assignee</label>
                            <select id="assignee_id" name="assignee_id" class="form-select">
                                <option value="">All Users</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Task Details Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskTitle">Task Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="taskBody">
                <!-- Dynamic content loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="taskLink" class="btn btn-primary">View Full Task</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
#calendar {
    max-width: 100%;
    margin: 0 auto;
    font-size: 14px; /* Ensure readable font size */
}
.fc-event {
    cursor: pointer;
    padding: 6px 8px; /* Increased padding for better title visibility */
    font-size: 14px; /* Larger font for readability */
    line-height: 1.5; /* Improved text spacing */
    border-radius: 4px; /* Smoother edges */
}
.fc-event:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}
.fc-daygrid-event .fc-event-main {
    white-space: normal !important; /* Allow text wrapping */
    overflow: visible !important; /* Prevent title clipping */
    min-height: 24px; /* Ensure space for title */
    display: block !important; /* Force visibility */
}
.fc-daygrid-day-events {
    min-height: 60px; /* More space for events */
}
.fc-daygrid-day-frame {
    min-height: 100px !important; /* Ensure day cells have enough height */
}
.fc-event-title {
    display: block !important; /* Force title visibility */
    color: #ffffff !important; /* White text for contrast */
    font-weight: 600; /* Bolder for readability */
    overflow: visible !important;
    white-space: normal !important;
}
.fc-daygrid-event {
    margin: 2px 0; /* Add spacing between events */
}
@media (max-width: 768px) {
    #calendar {
        font-size: 12px; /* Adjust for mobile */
    }
    .fc-event {
        padding: 4px 6px; /* Adjusted padding for mobile */
        font-size: 12px; /* Smaller font for mobile */
    }
    .fc-daygrid-day-events {
        min-height: 50px; /* Adjust for mobile */
    }
    .fc-daygrid-day-frame {
        min-height: 80px !important; /* Adjust for mobile */
    }
}
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if FullCalendar is loaded
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar is not defined. Check CDN or script path.');
        alert('Failed to load calendar library.');
        return;
    }

    let calendar = null;
    let currentFilters = {};

    function loadEvents(filters = {}) {
        console.log('Loading events with filters:', filters);
        currentFilters = filters;
        $.getJSON('{{ route("calendar.events") }}', filters, function(events) {
            console.log('Events received:', events);
            if (calendar) {
                calendar.removeAllEvents();
                calendar.addEventSource(events);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Failed to load events:', textStatus, errorThrown);
            alert('Failed to load calendar events.');
        });
    }

    // Initialize Calendar
    calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            loadEvents(Object.assign(currentFilters, {
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            }));
        },
        eventContent: function(arg) {
            console.log('Rendering event:', arg.event.title, 'ID:', arg.event.id);
            return {
                html: `
                    <div class="fc-event-title">${arg.event.title}</div>
                `
            };
        },
        eventClick: function(info) {
            console.log('Event clicked:', info.event.id, info.event.title);
            showTaskModal(info.event.id);
        },
        height: 'auto',
        eventDidMount: function(info) {
            console.log('Event mounted:', info.event.title, 'ID:', info.event.id);
            const titleEl = info.el.querySelector('.fc-event-title');
            if (titleEl) {
                titleEl.style.overflow = 'visible';
                titleEl.style.whiteSpace = 'normal';
                titleEl.style.display = 'block';
            } else {
                console.warn('Title element not found for event:', info.event.id);
            }
        }
    });
    calendar.render();
    console.log('Calendar rendered');

    // Filter Form
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        const filters = {
            status_id: $('#status_id').val(),
            priority: $('#priority').val(),
            assignee_id: $('#assignee_id').val()
        };
        console.log('Applying filters:', filters);
        loadEvents(filters);
    });

    // Task Modal
    function showTaskModal(taskId) {
        console.log('Fetching task details for ID:', taskId);
        $.getJSON('{{ route("calendar.task.show", ":id") }}'.replace(':id', taskId), function(task) {
            console.log('Task details loaded:', task);
            $('#taskTitle').text(task.title);
            $('#taskBody').html(`
                <p><strong>Priority:</strong> <span class="badge bg-${task.priority === 'critical' ? 'danger' : (task.priority === 'high' ? 'warning' : (task.priority === 'medium' ? 'info' : 'success'))}">${task.priority.charAt(0).toUpperCase() + task.priority.slice(1)}</span></p>
                <p><strong>Status:</strong> <span class="badge" style="background-color: ${task.status.color || '#007bff'}">${task.status.name}</span></p>
                <p><strong>Due Date:</strong> ${new Date(task.due_date).toLocaleDateString()}</p>
                <p><strong>Assignees:</strong> ${task.assignees.map(u => u.name).join(', ') || 'None'}</p>
                <p><strong>Description:</strong> ${task.description || 'No description'}</p>
            `);
            $('#taskLink').attr('href', `{{ route("boards.tasks.show", [":board", ":task"]) }}`.replace(':board', task.board_id).replace(':task', task.id)).text('View Full Task');
            new bootstrap.Modal(document.getElementById('taskModal')).show();
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.error('Failed to load task details:', textStatus, errorThrown);
            alert('Failed to load task details.');
        });
    }
});
</script>
@endpush
