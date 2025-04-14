<?php
class Task {
    private $conn;
    private $table_name = 'tasks';

    public $id;
    public $title;
    public $description;
    public $due_date;
    public $create_date;
    public $status;
    public $priority;
    public $category;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET title=:title, description=:description, due_date=:due_date, 
                      create_date=:create_date, status=:status, 
                      priority=:priority, category=:category";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":create_date", $this->create_date);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":category", $this->category);

        return $stmt->execute();
    }

    public function getAll($search = '', $sort = '', $page = 1, $perPage = 10) {
        $offset = ($page - 1) * $perPage;
        $query = "SELECT * FROM " . $this->table_name;

        if ($search) {
            $query .= " WHERE title LIKE :search";
        }

        if ($sort) {
            $query .= " ORDER BY " . $sort;
        }

        $query .= " LIMIT :offset, :perPage";

        $stmt = $this->conn->prepare($query);

        if ($search) {
            $search_param = "%" . $search . "%";
            $stmt->bindParam(":search", $search_param);
        }

        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindParam(":perPage", $perPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title=:title, description=:description, due_date=:due_date, 
                      priority=:priority, status=:status
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":due_date", $this->due_date);
        $stmt->bindParam(":priority", $this->priority);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
}
?>