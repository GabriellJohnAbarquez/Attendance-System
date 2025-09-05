<?php
require_once "Classes/User.php";

class Admin extends User {
    public function addCourse($course_name) {
        $stmt = $this->pdo->prepare("INSERT INTO courses (course_name) VALUES (?)");
        return $stmt->execute([$course_name]);
    }

    public function getCourses() {
        return $this->pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
    }

   public function viewAttendanceByCourseYear($course_id, $year_level) {
    $stmt = $this->pdo->prepare(
        "SELECT u.username, a.date, a.time_in, a.status, c.course_name, a.year_level
         FROM attendance a
         JOIN users u ON a.user_id = u.id
         JOIN courses c ON a.course_id = c.id
         WHERE a.course_id = ? AND a.year_level = ?
         ORDER BY a.date DESC, a.time_in DESC"
    );
    $stmt->execute([$course_id, $year_level]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
