@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">Create Team</h1>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="{{ route('teams.store') }}" method="POST">
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">Team Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Team</button>
                    <a href="{{ route('teams.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 10px;
        }
    </style>
@endsection
