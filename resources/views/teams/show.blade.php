@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">{{ $team->name }}</h1>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">Members</h2>
                        <div>
                            <input type="text" id="memberSearch" class="form-control d-inline-block w-auto" placeholder="Search members...">
                            <button class="btn btn-light ms-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                                <i class="bi bi-plus-circle"></i> Add Member
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($team->users->isEmpty())
                            <p class="text-muted">No members in this team.</p>
                        @else
                            <table class="table table-hover" id="membersTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->users as $member)
                                        <tr>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->email }}</td>
                                            <td>
                                                <span class="badge bg-{{ $member->pivot->role === 'owner' ? 'success' : ($member->pivot->role === 'admin' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($member->pivot->role) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($member->pivot->role !== 'owner')
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editRoleModal" data-user-id="{{ $member->id }}" data-role="{{ $member->pivot->role }}">
                                                        <i class="bi bi-pencil"></i> Edit Role
                                                    </button>
                                                    <form action="{{ route('teams.removeMember', [$team, $member->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this member?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="bi bi-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">Owner</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Member Modal -->
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('teams.addMember', $team) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addMemberModalLabel">Add Member to {{ $team->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
                                <label for="email" class="form-label">User Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <small class="form-text text-muted">Enter the email of the user to add.</small>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="member">Member</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Member</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Role Modal -->
        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editRoleForm" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="user_id" id="editUserId">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editRoleModalLabel">Edit Member Role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
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
                                <label for="editRole" class="form-label">Role</label>
                                <select class="form-select" id="editRole" name="role">
                                    <option value="member">Member</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card {
            border-radius: 10px;
        }
        .card-header {
            border-radius: 10px 10px 0 0;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .badge {
            font-size: 0.9em;
        }
        .btn-sm {
            margin-right: 5px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .modal-header {
            background-color: #f8f9fa;
        }
        #memberSearch {
            max-width: 200px;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editRoleModal = document.getElementById('editRoleModal');
            editRoleModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const userId = button.getAttribute('data-user-id');
                const role = button.getAttribute('data-role');
                const modal = this;
                modal.querySelector('#editUserId').value = userId;
                modal.querySelector('#editRole').value = role;
                modal.querySelector('#editRoleForm').action = `{{ route('teams.updateMember', [$team, 'USER_ID']) }}`.replace('USER_ID', userId);
            });

            // Client-side search for members
            const searchInput = document.getElementById('memberSearch');
            const table = document.getElementById('membersTable');
            if (table) {
                searchInput.addEventListener('input', function () {
                    const filter = this.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        const name = row.cells[0].textContent.toLowerCase();
                        const email = row.cells[1].textContent.toLowerCase();
                        row.style.display = (name.includes(filter) || email.includes(filter)) ? '' : 'none';
                    });
                });
            }
        });
    </script>
@endpush
