<?php
header("Content-Type: application/json; charset=UTF-8");

require_once 'Database.php';
require_once 'Task.php';

$database = new Database();
$db = $database->getConnection();

$task = new Task($db);

$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $task->getById($_GET['id']);
            echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            $search = $_GET['search'] ?? '';
            $sort = $_GET['sort'] ?? '';
            $page = $_GET['page'] ?? 1;
            $perPage = 10;

            $stmt = $task->getAll($search, $sort, $page, $perPage);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($tasks);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        $task->title = $data->title;
        $task->description = $data->description ?? '';
        $task->due_date = $data->due_date;
        $task->create_date = date('Y-m-d H:i:s');
        $task->status = $data->status;
        $task->priority = $data->priority;
        $task->category = $data->category;

        echo json_encode($task->create() ?
            ["message" => "Task created successfully"] :
            ["message" => "Task creation failed"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        $task->id = $_GET['id'];
        $task->title = $data->title;
        $task->description = $data->description;
        $task->due_date = $data->due_date;
        $task->priority = $data->priority;
        $task->status = $data->status;

        echo json_encode($task->update() ?
            ["message" => "Task updated successfully"] :
            ["message" => "Task update failed"]);
        break;

    case 'DELETE':
        $task->id = $_GET['id'];
        echo json_encode($task->delete() ?
            ["message" => "Task deleted successfully"] :
            ["message" => "Task deletion failed"]);
        break;

    default:
        echo json_encode(["message" => "Method not allowed"]);
        break;
}
?>