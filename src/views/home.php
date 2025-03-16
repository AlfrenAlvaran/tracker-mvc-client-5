<?php if ($error): ?>
  <div class="container my-5">
    <div class="alert alert-danger" role="alert">
      <?= htmlspecialchars($error) ?>
    </div>
  </div>
<?php endif; ?>

<div class="container my-5 d-flex justify-content-between align-items-center">
  <h2 class="text-black fw-bold">Task</h2>
  <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#createTaskModal">Create Task</button>
</div>

<div class="container">
  <form method="GET" class="mb-3">
    <div class="input-group">
      <input type="text" name="search" class="form-control" placeholder="Search by title or status" value="<?= htmlspecialchars($search); ?>">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle">
      <thead class="bg-primary text-white">
        <tr>
          <th>Title</th>
          <th>Due Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tasks as $task) {
          $status = trim($task['status']);
          $status = ucfirst(strtolower($status));
          
          switch ($status) {
              case 'Not started':
                  $statusBadge = '<span class="badge bg-secondary"><i class="fas fa-circle-xmark"></i> Not Started</span>';
                  break;
              case 'In progress':
                  $statusBadge = '<span class="badge bg-warning text-dark"><i class="fas fa-spinner"></i> In Progress</span>';
                  break;
              case 'Complete': // Fix here (was 'Completed')
                  $statusBadge = '<span class="badge bg-success"><i class="fas fa-circle-check"></i> Completed</span>';
                  break;
              default:
                  $statusBadge = '<span class="badge bg-dark"><i class="fas fa-question-circle"></i> Unknown</span>';
                  break;
          }
          
          
        ?>
          <tr>
            <td><?= htmlspecialchars($task['title']); ?></td>
            <td><?= htmlspecialchars($task['due_date']); ?></td>
            <td><?= $statusBadge ?></td>
            <td>
              <a href="/view/<?= $task['Id']; ?>" class="btn btn-info btn-sm text-white">View</a>
              <a href="/delete/<?= $task['Id']; ?>" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

  <nav>
    <ul class="pagination">
      <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <li class="page-item <?= ($i == $currentPage) ? 'active' : ''; ?>">
          <a class="page-link" href="?search=<?= urlencode($search); ?>&page=<?= $i; ?>"><?= $i; ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>


<!-- MODAL -->

<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createTaskModalLabel">Create New Task</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Form -->
        <form action="" method="POST" enctype="multipart/form-data">
          <!-- Task Name -->
          <div class="mb-3">
            <label for="taskName" class="form-label">Task Name</label>
            <input type="text" name="title" class="form-control" id="taskName" placeholder="Enter task name">
          </div>
          <!-- Description -->
          <div class="mb-3">
            <label for="taskDescription" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="taskDescription" rows="3" placeholder="Enter task description"></textarea>
          </div>

          <!-- Due Date -->
          <div class="mb-3">
            <label for="dueDate" class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control" id="dueDate">
          </div>

          <div class="mb-3">
            <label for="file" class="form-label">Expected upload file</label>
            <input type="number" name="expected_files" min="1" class="form-control" id="file">
          </div>

          <!-- Save & Close Buttons -->
          <div class="text-end">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="create_task" class="btn btn-primary">Save Task</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- MODAL -->