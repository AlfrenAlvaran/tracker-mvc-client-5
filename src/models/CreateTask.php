<?php

namespace Tracker\Models;

use PDO;
use Tracker\Config\Database;

use Tracker\Services\MailService;

class CreateTask
{
    private $conn;
    private $mailService;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
        if ($this->conn == null) {
            die("Database Connection Failed");
        }
        $this->mailService = new MailService();
    }

    public function addTask($title, $description, $due_date, $expected_files)
    {
        $stmt = $this->conn->prepare("INSERT INTO task (title, description, due_date, expected_files, status) VALUES (?, ?, ?, ?, 'Not Started')");
        $stmt->execute([$title, $description, $due_date, $expected_files]);

        $taskID = $this->conn->lastInsertId();

       
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
        $uploadDir = __DIR__ . "/../../upload/$folderName/";

       
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                die("Failed to create upload directory: $uploadDir");
            }
        }

        return $taskID;
    }


    public function uploadMultipleFiles($id, $files, $name)
    {
        $stmt = $this->conn->prepare("SELECT title, expected_files FROM task WHERE Id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch();

        if (!$task) {
            die("Task not found!");
        }

        $title = $task['title'];
        $expected_files = $task['expected_files'];
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $task['title']);
        $uploadDir = __DIR__ . "/../../upload/$folderName/";

        if (!is_dir($uploadDir)) {
            die("Upload directory does not exist: $uploadDir");
        }

        foreach ($files['name'] as $index => $filename) {
            $filePath = $uploadDir . basename($filename);

            if (move_uploaded_file($files['tmp_name'][$index], $filePath)) {
                $stmt = $this->conn->prepare("INSERT INTO task_files (task_id, file_name, file_path) VALUES (?, ?, ?)");
                $stmt->execute([$id, $filename, $filePath]);
            } else {
                echo "Failed to move file: " . $files['tmp_name'][$index] . " -> " . $filePath . "<br>";
            }
        }

        
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM task_files WHERE task_id = ?");
        $stmt->execute([$id]);
        $uploaded_count = (int) $stmt->fetchColumn();

      
        
        if ($uploaded_count > 0 && $uploaded_count < $expected_files) {
            $status = "Progressing";
        } elseif ($uploaded_count >= $expected_files) {
            $status = "Complete";
        } else {
            $status = "Not Started";
        }
        var_dump($uploaded_count, $status); // Debugging

        $stmt = $this->conn->prepare("UPDATE task SET status = ? WHERE Id = ?");
        $stmt->execute([$status, $id]);
    }
}
