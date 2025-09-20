@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">My Teams</h1>
        <a href="{{ route('teams.create') }}" class="btn btn-primary mb-3">
            <i class="bi bi-plus-circle"></i> Create Team
        </a>
        <div class="card shadow-sm">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($teams->isEmpty())
                    <p class="text-muted">You are not part of any teams. Create one to get started!</p>
                @else
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Members</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teams as $team)
                                <tr>
                                    <td>{{ $team->name }}</td>
                                    <td>{{ $team->users->count() }}</td>
                                    <td>
                                        <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('teams.destroy', $team) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this team?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 10px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .btn-sm {
            margin-right: 5px;
        }
    </style>
@endsection
