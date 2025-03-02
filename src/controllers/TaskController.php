<?php

namespace Tracker\Controllers;

use Tracker\Models\TaskModel;
use Tracker\Models\CreateTask;
use Tracker\Models\TaskReminder;

class TaskController
{
    private $task;
    private $createTask;
    private $taskReminder;
    private $tasksPerPage = 5; // Number of tasks per page

    public function __construct()
    {
        $this->task = new TaskModel();
        $this->createTask = new CreateTask();
        $this->taskReminder = new TaskReminder();
    }

    public function renderTasks()
    {
        $error = '';
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $this->tasksPerPage;

        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['create_task'])) {
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $due_date = $_POST['due_date'];
            $expected_files = $_POST['expected_files'] ?? "1";

            if ($this->task->existed_task($title)) {
                $error = "Task already exists.";
            } else {
                $this->createTask->addTask($title, $description, $due_date, $expected_files);
                header("Location: /");
                exit();
            }
        }

        $tasks = $this->task->getPaginatedTasks($search, $this->tasksPerPage, $offset);
        $totalTasks = $this->task->countTasks($search);
        $totalPages = ceil($totalTasks / $this->tasksPerPage);

        $content = $this->renderView('home', [
            'tasks' => $tasks,
            'error' => $error,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'search' => $search
        ]);
        $this->renderLayout('Home', $content);
    }

    public function deleteTask($id)
    {
        $this->task->deleteTask($id);
        header('Location: /');
    }

    public function viewTask($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['upload_files'])) {
            $task_id = $_POST['task_id'];
            $name = $_POST['name'];
            $documents = $_FILES['files'];

            if (!empty($documents)) {
                $this->createTask->uploadMultipleFiles($task_id, $documents, $name);
            }
        }

        $task = $this->task->getTaskByID($id);
        $files = $this->task->getTaskFilesById($id);

        $content = $this->renderView('view', ['task' => $task, 'files' => $files]);
        $this->renderLayout('View Task', $content);
    }

    public function renderView($view, $data = [])
    {
        extract($data);
        ob_start();
        require_once __DIR__ . "/../views/$view.php";
        return ob_get_clean();
    }

    public function renderLayout($title, $content)
    {
        ob_start();
        require_once __DIR__ . "/../views/layouts/layout.php";
        echo ob_get_clean();
    }
}
