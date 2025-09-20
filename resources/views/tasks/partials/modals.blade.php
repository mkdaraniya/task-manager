{{-- Create Task Modal --}}
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="createTaskForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="low">Low</option>
              <option value="medium" selected>Medium</option>
              <option value="high">High</option>
              <option value="critical">Critical</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Due date</label>
            <input type="date" name="due_date" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Assignees</label>
            <select name="assignees[]" class="form-select" multiple>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Tags</label>
            <select name="tags[]" class="form-select" multiple>
              @foreach($tags as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
              @endforeach
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Edit Task Modal --}}
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editTaskForm">
        @csrf
        @method('PUT')
        <input type="hidden" id="editTaskId" name="id">
        <div class="modal-header">
          <h5 class="modal-title">Edit Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label">Title <span class="text-danger">*</span></label>
            <input type="text" id="editTaskTitle" name="title" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea id="editTaskDescription" name="description" class="form-control" rows="4"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select id="editTaskPriority" name="priority" class="form-select">
              <option value="low">Low</option>
              <option value="medium">Medium</option>
              <option value="high">High</option>
              <option value="critical">Critical</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Due date</label>
            <input type="date" id="editTaskDueDate" name="due_date" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">Assignees</label>
            <select id="editTaskAssignees" name="assignees[]" class="form-select" multiple>
              @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
              @endforeach
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
