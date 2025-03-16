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
        // $this->mailService = new MailService();
    }

    public function addTask($title, $description, $due_date, $expected_files)
    {
        $stmt = $this->conn->prepare("INSERT INTO task (title, description, due_date, expected_files, status) VALUES (?, ?, ?, ?, 'Not Started')");
        $stmt->execute([$title, $description, $due_date, $expected_files]);

        $taskID = $this->conn->lastInsertId();

       
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title). "_" . $taskID;
        $baseUploadDir = realpath(__DIR__ . "/../../upload");

       
        // if (!is_dir($uploadDir)) {
        //     if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
        //         die("Failed to create upload directory: $uploadDir");
        //     }
        // }
        if ($baseUploadDir === false) {
            die("Base upload directory does not exist: " . __DIR__ . "/../../upload");
        }

        $uploadDir = $baseUploadDir . "/$folderName/";

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            die("Failed to create upload directory: $uploadDir. Check permissions.");
        }

        

        return $taskID;
    }


    public function uploadMultipleFiles($id, $files)
    {
     
        $stmt = $this->conn->prepare("SELECT title, expected_files FROM task WHERE Id = ?");
        $stmt->execute([$id]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            die("Task not found!");
        }

        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $task['title']) . "_" . $id;
        $baseUploadDir = realpath(__DIR__ . "/../../upload");

        if ($baseUploadDir === false) {
            die("Base upload directory does not exist: " . __DIR__ . "/../../upload");
        }

        $uploadDir = $baseUploadDir . "/$folderName/";

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
        $expected_files = (int) $task['expected_files'];

        if ($uploaded_count > 0 && $uploaded_count < $expected_files) {
            $status = "Progressing";
        } elseif ($uploaded_count >= $expected_files) {
            $status = "Complete";
        } else {
            $status = "Not Started";
        }

        $stmt = $this->conn->prepare("UPDATE task SET status = ? WHERE Id = ?");
        $stmt->execute([$status, $id]);
    }
}
