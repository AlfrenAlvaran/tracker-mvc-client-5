<?php

namespace Tracker\Models;

use PDO;
use Tracker\Config\Database;

class TaskModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
        if ($this->conn == null) {
            die("Database Connection Failed");
        }
    }

    public function deleteTask($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM task WHERE Id = ?");
        $stmt->execute([$id]);
    }

    public function getPaginatedTasks($search, $limit, $offset)
{
    $sql = "SELECT * FROM task";
    $params = [];

    if (!empty($search)) {
        $sql .= " WHERE title LIKE ? OR status LIKE ?";
        $params = ["%$search%", "%$search%"];
    }

    $sql .= " ORDER BY due_date ASC LIMIT ? OFFSET ?";
    
    $stmt = $this->conn->prepare($sql);
    
    // Bind values properly
    foreach ($params as $index => $param) {
        $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
    }
    
    $stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function countTasks($search)
    {
        $sql = "SELECT COUNT(*) FROM task";
        $params = [];

        if (!empty($search)) {
            $sql .= " WHERE title LIKE ? OR status LIKE ?";
            $params = ["%$search%", "%$search%"];
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function existed_task($title)
    {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE title = ?");
        $stmt->execute([$title]);
        return $stmt->fetch();
    }

    public function getTaskByID($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM task WHERE Id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getTaskFilesById($id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM task_files WHERE task_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
