@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Admin Panel</h2>
    <div class="row">
        <div class="col-md-6">
            <h4>Users</h4>
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Roles</th></tr></thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @foreach($user->roles as $role)
                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                @endforeach
                                <form method="POST" action="{{ route('admin.assign-role', $user) }}" class="d-inline">
                                    @csrf
                                    <select name="role" class="form-select form-select-sm d-inline w-auto">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Assign</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <h4>System Logs</h4>
            <a href="{{ route('admin.logs') }}" class="btn btn-secondary">View Logs</a>
        </div>
    </div>
</div>
@endsection
