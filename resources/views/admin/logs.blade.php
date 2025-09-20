@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4 fw-bold">Activity Logs</h2>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Details</th>
                                <th>Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr>
                                    <td>{{ $log->user->name }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        @if ($log->properties)
                                            <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection
