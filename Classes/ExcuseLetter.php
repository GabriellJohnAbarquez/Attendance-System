<?php
class ExcuseLetter {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Student submits excuse letter
    public function submitLetter($student_id, $course_id, $year_level, $reason) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO excuse_letters (student_id, course_id, year_level, reason) 
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$student_id, $course_id, $year_level, $reason]);
    }

    // Student views own letters
    public function getStudentLetters($student_id) {
        $stmt = $this->pdo->prepare(
            "SELECT el.*, c.course_name 
             FROM excuse_letters el
             JOIN courses c ON el.course_id = c.id
             WHERE el.student_id = ?
             ORDER BY el.date_submitted DESC"
        );
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin views all letters by course/year
    public function getLettersByCourseYear($course_id, $year_level) {
        $stmt = $this->pdo->prepare(
            "SELECT el.*, u.username, c.course_name
             FROM excuse_letters el
             JOIN users u ON el.student_id = u.id
             JOIN courses c ON el.course_id = c.id
             WHERE el.course_id = ? AND el.year_level = ?
             ORDER BY el.date_submitted DESC"
        );
        $stmt->execute([$course_id, $year_level]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Admin updates status
    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare(
            "UPDATE excuse_letters SET status = ? WHERE id = ?"
        );
        return $stmt->execute([$status, $id]);
    }
}
?>
