<?php
class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM courses ORDER BY course_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($course_name) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (course_name, status) VALUES (?, 'open')");
        return $stmt->execute([$course_name]);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE courses SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOpenCourses() {
    $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE status = 'open' ORDER BY course_name ASC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
