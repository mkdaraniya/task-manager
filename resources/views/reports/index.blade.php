@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Generate Reports</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <form method="POST" action="{{ route('reports.generate') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Report Type</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="" selected disabled>Select report type</option>
                            <option value="task-progress" {{ old('type') == 'task-progress' ? 'selected' : '' }}>Task Progress</option>
                            <option value="user-activity" {{ old('type') == 'user-activity' ? 'selected' : '' }}>User Activity</option>
                            <option value="ticket-status" {{ old('type') == 'ticket-status' ? 'selected' : '' }}>Ticket Status</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="format" class="form-label">Format</label>
                        <select name="format" class="form-select @error('format') is-invalid @enderror" required>
                            <option value="" selected disabled>Select format</option>
                            <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                        </select>
                        @error('format')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control @error('date_from') is-invalid @enderror" value="{{ old('date_from') }}">
                        @error('date_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control @error('date_to') is-invalid @enderror" value="{{ old('date_to') }}">
                        @error('date_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="user" class="form-label">User</label>
                        <select name="user" class="form-select @error('user') is-invalid @enderror">
                            <option value="" selected>All Users</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="project" class="form-label">Project</label>
                        <select name="project" class="form-select @error('project') is-invalid @enderror">
                            <option value="" selected>All Projects</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                            @endforeach
                        </select>
                        @error('project')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="" selected>All Statuses</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" {{ old('status') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .card {
            border-radius: 10px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .form-label {
            font-weight: 500;
        }
        .form-select, .form-control {
            border-radius: 6px;
        }
        .btn-primary {
            border-radius: 6px;
        }
    </style>
@endsection
