@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h2>Projects</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">+ New Project</button>
        </div>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="projectTableBody">
                @foreach ($projects as $project)
                    <tr id="row-{{ $project->id }}">
                        <td>{{ $project->id }}</td>
                        <td class="name">{{ $project->name }}</td>
                        <td class="description">{{ $project->description }}</td>
                        <td class="status">
                            <span
                                class="badge bg-{{ $project->status == 'active' ? 'success' : ($project->status == 'pending' ? 'warning' : 'secondary') }}">
                                {{ $project->status }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning edit-btn" data-id="{{ $project->id }}"
                                data-name="{{ htmlspecialchars($project->name, ENT_QUOTES) }}"
                                data-description="{{ htmlspecialchars($project->description ?? '', ENT_QUOTES) }}"
                                data-status="{{ $project->status }}" onclick="openEditModalBtn(this)">
                                Edit
                            </button>

                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $project->id }}"
                                onclick="confirmDeleteBtn(this)">
                                Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @include('projects.partials.modals')
@endsection


@push('scripts')
    <script>
        (function() {
            // Quick sanity logs
            console.log('[projects] script start');

            // Warn if bootstrap not present (Bootstrap bundle should be loaded in layout)
            if (typeof bootstrap === 'undefined') {
                console.warn(
                    '[projects] bootstrap is undefined — ensure bootstrap.bundle.min.js is loaded before @stack("scripts") in layout.'
                    );
            }

            // Ensure jQuery present
            if (typeof $ === 'undefined') {
                console.warn('[projects] jQuery ($) is undefined — ensure jQuery is loaded in layout.');
            }

            // Helper: get or create bootstrap modal instance
            function getModalInstance(selector) {
                const el = document.querySelector(selector);
                if (!el) return null;
                return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            }

            // Common AJAX error handler
            function handleAjaxError(xhr) {
                let message = 'An error occurred';
                try {
                    if (xhr && xhr.responseJSON) {
                        if (xhr.responseJSON.message) message = xhr.responseJSON.message;
                        else if (xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join(
                            ', ');
                    } else if (xhr && xhr.status) {
                        message = 'Server error: ' + xhr.status;
                    }
                } catch (e) {
                    console.error('[projects] error parsing xhr', e);
                }
                console.error('[projects] AJAX error:', xhr);
                if (typeof Swal !== 'undefined') Swal.fire('Error', message, 'error');
                else alert(message);
            }

            // ----- Global fallback functions (ensure these exist on window) -----
            window.openEditModalBtn = function(btnEl) {
                try {
                    const el = btnEl;
                    const projectId = el.getAttribute('data-id');
                    const projectName = el.getAttribute('data-name') || '';
                    const projectDescription = el.getAttribute('data-description') || '';
                    const projectStatus = el.getAttribute('data-status') || '';

                    console.log('[openEditModalBtn]', projectId);

                    const idInput = document.getElementById('editProjectId');
                    const nameInput = document.getElementById('editProjectName');
                    const descInput = document.getElementById('editProjectDescription');
                    const statusInput = document.getElementById('editProjectStatus');

                    if (idInput) idInput.value = projectId;
                    if (nameInput) nameInput.value = projectName;
                    if (descInput) descInput.value = projectDescription;
                    if (statusInput) statusInput.value = projectStatus;

                    const modal = getModalInstance('#editProjectModal');
                    if (modal) modal.show();
                    else console.warn('[openEditModalBtn] #editProjectModal not found');
                } catch (err) {
                    console.error('[openEditModalBtn] error', err);
                }
            };

            window.confirmDeleteBtn = function(btnEl) {
                try {
                    const el = btnEl;
                    const projectId = el.getAttribute('data-id');
                    console.log('[confirmDeleteBtn]', projectId);

                    if (!projectId) {
                        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Project ID not found', 'error');
                        else alert('Project ID not found');
                        return;
                    }

                    const doDelete = function() {
                        $.ajax({
                                url: "{{ url('projects') }}/" + projectId,
                                type: "POST",
                                data: {
                                    _method: 'DELETE',
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                dataType: 'json'
                            })
                            .done(function(response) {
                                console.log('[confirmDeleteBtn] response', response);
                                $('#row-' + projectId).fadeOut(250, function() {
                                    $(this).remove();
                                });
                                if (typeof Swal !== 'undefined') Swal.fire('Archived!', response.message ||
                                    'Project archived', 'success');
                            })
                            .fail(function(xhr) {
                                handleAjaxError(xhr);
                            });
                    };

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: 'This will archive the project.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, archive it!'
                        }).then((result) => {
                            if (result.isConfirmed) doDelete();
                        });
                    } else {
                        if (confirm('Are you sure? This will archive the project.')) doDelete();
                    }
                } catch (err) {
                    console.error('[confirmDeleteBtn] error', err);
                }
            };

            // ----- delegated handlers (for dynamic rows) -----
            $(document).ready(function() {
                console.log('[projects] DOM ready — attaching delegated handlers');

                // Ensure CSRF header set
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Delegated click handlers call the same global functions
                $(document).on('click', '.edit-btn', function(e) {
                    // If onclick already fired it's OK — this is safe duplicate
                    openEditModalBtn(this);
                });

                $(document).on('click', '.delete-btn', function(e) {
                    confirmDeleteBtn(this);
                });

                // Edit form submit (AJAX update)
                $(document).on('submit', '#editProjectForm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $(this);
                    const projectId = $('#editProjectId').val();
                    const submitBtn = form.find('button[type="submit"]');
                    const formData = form.serialize();

                    if (!projectId) {
                        if (typeof Swal !== 'undefined') Swal.fire('Error', 'Project ID not found',
                            'error');
                        else alert('Project ID not found');
                        return false;
                    }

                    submitBtn.prop('disabled', true).text('Updating...');

                    $.ajax({
                            url: "{{ url('projects') }}/" + projectId,
                            type: "POST",
                            data: formData,
                            dataType: 'json'
                        })
                        .done(function(response) {
                            console.log('[editProjectForm] update', response);
                            if (response.success) {
                                const project = response.data;
                                $('#row-' + projectId).replaceWith(renderProjectRow(project));
                                const modal = getModalInstance('#editProjectModal');
                                if (modal) modal.hide();
                                if (typeof Swal !== 'undefined') Swal.fire('Success', response
                                    .message, 'success');
                            }
                        })
                        .fail(function(xhr) {
                            handleAjaxError(xhr);
                        })
                        .always(function() {
                            submitBtn.prop('disabled', false).text('Update');
                        });

                    return false;
                });

                // Create form submit is unchanged (keep modal hide via BS5)
                $(document).on('submit', '#createProjectForm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const form = $(this);
                    const submitBtn = form.find('button[type="submit"]');
                    const formData = form.serialize();

                    submitBtn.prop('disabled', true).text('Creating...');

                    $.ajax({
                            url: "{{ route('projects.store') }}",
                            type: "POST",
                            data: formData,
                            dataType: 'json'
                        })
                        .done(function(response) {
                            if (response.success) {
                                const project = response.data;
                                $('#projectTableBody').prepend(renderProjectRow(project));
                                const createModal = getModalInstance('#createProjectModal');
                                if (createModal) createModal.hide();
                                form[0].reset();
                                if (typeof Swal !== 'undefined') Swal.fire('Success', response
                                    .message, 'success');
                            }
                        })
                        .fail(function(xhr) {
                            handleAjaxError(xhr);
                        })
                        .always(function() {
                            submitBtn.prop('disabled', false).text('Create');
                        });

                    return false;
                });
            });

            // ----- client render helper (used after create/update) -----
            window.renderProjectRow = function(project) {
                function esc(s) {
                    if (s === null || typeof s === 'undefined') return '';
                    return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(
                        /</g, '&lt;').replace(/>/g, '&gt;');
                }
                const statusClass = project.status === 'active' ? 'success' : (project.status === 'pending' ?
                    'warning' : 'secondary');

                return `
            <tr id="row-${project.id}">
                <td>${project.id}</td>
                <td class="name">${project.name}</td>
                <td class="description">${project.description || ''}</td>
                <td class="status"><span class="badge bg-${statusClass}">${project.status}</span></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning edit-btn"
                        data-id="${project.id}"
                        data-name="${esc(project.name)}"
                        data-description="${esc(project.description || '')}"
                        data-status="${project.status}"
                        onclick="openEditModalBtn(this)">
                        Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                        data-id="${project.id}"
                        onclick="confirmDeleteBtn(this)">
                        Delete
                    </button>
                </td>
            </tr>
        `;
            };

        })();
    </script>
@endpush
