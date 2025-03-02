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
            $subject = "🚀 Reminder: {$task['title']} is Due Soon!";
            $message = "<h2 style='color:#007bff;'>Task Reminder</h2>";
            $message .= "<p><strong>Task:</strong> {$task['title']}</p>";
            $message .= "<p><strong>Due Date:</strong> {$task['due_date']}</p>";
            $message .= "<p>{$task['description']}</p>";
            $message .= "<p style='color:red; font-weight:bold;'>This is an automated reminder.</p>";

            $this->mailService->sendReminderEmail(getenv('EMAIL_TO'), $subject, $message);
        }
    }
}
?>

