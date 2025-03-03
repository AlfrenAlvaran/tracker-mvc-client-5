<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->load();

use Tracker\Controllers\TaskController;
use Tracker\Models\TaskReminder;


$path = $_SERVER['PATH_INFO'] ?? '/';
$parts = explode('/', $path);
$action = $parts[1] ?? '/';
$id = $parts[2] ?? null;

$controller = new TaskController();

$reminder = new TaskReminder();
$reminder->sendReminders();




switch($action) {
    case '';
         $controller->renderTasks();
        break;
    case 'view':
        $controller->viewTask($id);
        break;
    case 'delete':
        $controller->deleteTask($id);
        break;
    
}