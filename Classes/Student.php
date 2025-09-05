<?php
require_once "Classes/User.php";

class Student extends User {
    public $course_id, $year_level;

    public function fileAttendance() {
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Fetch student's course and year_level
    $stmt = $this->pdo->prepare("SELECT course_id, year_level FROM users WHERE id = ?");
    $stmt->execute([$this->id]);
    $studentData = $stmt->fetch(PDO::FETCH_ASSOC);

    $course_id = $studentData['course_id'];
    $year_level = $studentData['year_level'];

    // Determine status: late if after 08:00 AM
    $status = ($time > "08:00:00") ? "Late" : "On Time";

    $stmt = $this->pdo->prepare(
        "INSERT INTO attendance (user_id, course_id, year_level, date, time_in, status) 
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    return $stmt->execute([$this->id, $course_id, $year_level, $date, $time, $status]);
}


    public function viewHistory() {
    $stmt = $this->pdo->prepare(
        "SELECT a.date, a.time_in, a.status, c.course_name, a.year_level
         FROM attendance a
         LEFT JOIN courses c ON a.course_id = c.id
         WHERE a.user_id = ?
         ORDER BY a.date DESC, a.time_in DESC"
    );
    $stmt->execute([$this->id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
