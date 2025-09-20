@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $task->title }}</h2>
    <div class="row">
        <div class="col-md-8">
            <div>{!! TaskController::markdown($task->description) !!}</div>  <!-- Markdown rendered -->
            <hr>
            <!-- Assignees badges -->
            @foreach($task->assignees as $assignee)
                <span class="badge bg-primary">{{ $assignee->name }}</span>
            @endforeach
            <!-- Tags -->
            @foreach($task->tags as $tag)
                <span class="badge" style="background: {{ $tag->color }}">{{ $tag->name }}</span>
            @endforeach

            <!-- Subtasks checklist -->
            <h5>Subtasks</h5>
            <ul class="list-group">
                @foreach($task->subtasks as $subtask)
                    <li class="list-group-item">
                        <input type="checkbox" {{ $subtask->completed ? 'checked' : '' }} onchange="toggleSubtask({{ $subtask->id }})">
                        {{ $subtask->title }}
                    </li>
                @endforeach
            </ul>
            <button class="btn btn-sm btn-outline-primary" onclick="addSubtask()">Add Subtask</button>

            <!-- Comments threaded -->
            <h5>Comments</h5>
            <div id="comments">
                @foreach($task->comments->whereNull('parent_id') as $comment)
                    <div class="comment-thread">
                        <div class="comment">
                            <strong>{{ $comment->user->name }}</strong> {{ $comment->created_at->diffForHumans() }}
                            <p>{!! TaskController::markdown($comment->body) !!}</p>
                            <button class="btn btn-sm" onclick="reply({{ $comment->id }})">Reply</button>
                        </div>
                        @include('tasks.partials.replies', ['replies' => $comment->replies])
                    </div>
                @endforeach
            </div>
            <form id="commentForm">
                @csrf
                <textarea name="body" class="form-control" placeholder="Add comment... (use @username)"></textarea>
                <button type="submit" class="btn btn-primary mt-2">Send</button>
            </form>

            <!-- Time tracking -->
            <h5>Time Logs</h5>
            <table class="table">
                <thead><tr><th>User</th><th>Hours</th><th>Description</th></tr></thead>
                <tbody>
                    @foreach($task->timeLogs as $log)
                        <tr><td>{{ $log->user->name }}</td><td>{{ $log->hours }}</td><td>{{ $log->description }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            <form id="timeLogForm">
                @csrf
                <input type="number" name="hours" step="0.1" placeholder="Hours" required>
                <textarea name="description" placeholder="Description"></textarea>
                <button type="submit" class="btn btn-primary">Log Time</button>
            </form>
        </div>
        <div class="col-md-4">
            <!-- Attachments -->
            <h5>Attachments</h5>
            @foreach($task->attachments as $attachment)
                <a href="{{ Storage::url($attachment->path) }}" class="d-block">{{ $attachment->original_name }}</a>
            @endforeach
            <input type="file" id="fileUpload" onchange="uploadFile({{ $task->id }}, 'tasks')">
        </div>
    </div>
</div>

@push('scripts')
<script>
    // AJAX for comments
    document.getElementById('commentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('task_id', {{ $task->id }});
        fetch('{{ route("tasks.comments.store", $task) }}', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(comment => {
            document.getElementById('comments').insertAdjacentHTML('beforeend', `
                <div class="comment-thread">
                    <div class="comment">
                        <strong>${comment.user.name}</strong> just now
                        <p>${marked.parse(comment.body)}</p>
                    </div>
                </div>
            `);
            this.reset();
        });
    });

    // Similar for subtasks, time logs, file upload with FormData

    function uploadFile(id, type) {
        const file = document.getElementById('fileUpload').files[0];
        const formData = new FormData();
        formData.append('file', file);
        fetch(`/attachments?attachable_id=${id}&type=${type}`, {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(attachment => {
            // Append link
        });
    }
</script>
@endpush
@endsection
