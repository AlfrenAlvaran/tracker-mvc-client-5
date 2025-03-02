<div class="container my-5 d-flex justify-content-between align-items-center">
    <h2 class="text-black fw-bold">Task</h2>
    <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#createTaskModal">Upload File</button>
</div>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">

        <div class="card  border-0 w-100">
            <div class="card-header bg-primary text-white text-center">
                <h2 class="mb-0">User Information</h2>
            </div>
            <div class="card-body p-5">
                <div class="row">

                    <?php if (!empty($files)): ?>
                        <?php foreach ($files as $file) : ?>
                            <div class="col-md-6 mb-3">
                                <div class="p-3 bg-light rounded shadow-sm">
                                    <h5 class="mb-1 text-secondary">Name</h5>
                                    <p class="mb-0 fw-bold"><?= isset($file['file_name']) ? htmlspecialchars($file['file_name']) : 'N/A' ?></p>
                                    <p class="mb-0 fw-bold"><b>Uploaded At</b> <?= isset($file['uploaded_at']) ? htmlspecialchars($file['uploaded_at']) : 'N/A' ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No files uploaded for this task.</p>
                    <?php endif; ?>

                </div>
                <div class="text-center mt-4">
                    <a href="/" class="btn btn-primary px-5 py-2 fw-bold">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



<!-- Modal -->
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
                        <label for="taskName" class="form-label">File Name</label>
                        <input type="text" name="name" class="form-control" id="taskName" placeholder="Enter task name">
                    </div>
                    <input type="hidden" name="task_id" value="<?= htmlspecialchars($task['Id']) ?>">
                    <div class="mb-3">
                        <label for="taskName" class="form-label">Select Files</label>
                        <input type="file" name="files[]" class="form-control" multiple>
                    </div>
                    <!-- Save & Close Buttons -->
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="upload_files" class="btn btn-primary">Save Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->