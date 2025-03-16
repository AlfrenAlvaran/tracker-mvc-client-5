<?php

namespace Tracker\Models;

use Tracker\Services\MailService;
use Tracker\Config\Database;
use PDO;

class TaskReminder
{
    private $conn;
    private $mailService;

    public function __construct()
    {
        $this->conn = (new Database())->connect();
        $this->mailService = new MailService();
    }

    public function sendReminders()
    {
        $today = date('Y-m-d');
    
        $query = "SELECT * FROM task WHERE 
                  (DATE(due_date) = DATE_ADD(?, INTERVAL 2 DAY) OR 
                   DATE(due_date) = DATE_ADD(?, INTERVAL 4 DAY)) 
                  AND status != 'Completed'";
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$today, $today]);
    
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($tasks as $task) {
            if ($this->isReminderSent($task['title'], $task['due_date'])) {
                continue;
            }
    
            $subject = "🚀 Reminder: {$task['title']} is Due Soon!";
            $message = "
                <h2 style='color:#007bff;'>Task Reminder</h2>
                <p><strong>Task Name:</strong> {$task['title']}</p>
                <p><strong>Due Date:</strong> {$task['due_date']}</p>
                <p style='color:red;'>This is an automated reminder.</p>
            ";
    
            $this->mailService->sendReminderEmail($subject, $message);
            $this->logReminder($task['title'], $task['due_date']);
        }
    }
    
    public function isReminderSent($title, $due_date)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reminder_log WHERE title = ? AND due_date = ?");
        $stmt->execute([$title, $due_date]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function logReminder($title, $due_date)
    {
        $stmt = $this->conn->prepare("INSERT INTO reminder_log (title, due_date) VALUES (?, ?)");
        $stmt->execute([$title, $due_date]);
    }
    
    
    
    
    public function successfulCreatedTask($task_name, $due_date, $expected_files)
    {
       if(!$this->isReminderSent($task_name,$due_date )){
        $subject = "You have a new task: $task_name";
        $message = "<h2 style='color:#007bff;'>Task Created Successfully</h2>";
        $message .= "<p><strong>Task:</strong> $task_name</p>";
        $message .= "<p><strong>Due Date:</strong> $due_date</p>";
        $message .= "<p>Expected upload: $expected_files</p>";
        $message .= "<p style='color:red; font-weight:bold;'>This is an automated reminder.</p>";

       
        $this->mailService->sendReminderEmail($subject, $message);
        $this->LogReminder($task_name, $due_date);
       }
    }

    // public function isReminderSent($title, $due_date) {
    //     $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reminder_log WHERE title = ? AND due_date = ?");
    //     $stmt->execute([$title, $due_date]);
    //     return $stmt->fetchColumn() > 0;
    // }
    // public function LogReminder($title, $due_date) {
    //     $stmt = $this->conn->prepare("INSERT INTO reminder_log (title, due_date) VALUES (?, ?)");
    //     $stmt->execute([$title, $due_date]);
    // }
}
