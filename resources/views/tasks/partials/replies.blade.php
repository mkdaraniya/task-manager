@foreach($replies as $reply)
    <div class="comment ml-4">
        <strong>{{ $reply->user->name }}</strong> {{ $reply->created_at->diffForHumans() }}
        <p>{!! TaskController::markdown($reply->body) !!}</p>
        <button class="btn btn-sm" onclick="reply({{ $reply->id }})">Reply</button>
        @include('tasks.partials.replies', ['replies' => $reply->replies])
    </div>
@endforeach
