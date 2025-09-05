<?php
class User {
    protected $pdo;
    public $id, $username, $role;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function register($username, $password, $role, $course_id = null, $year_level = null) {
        $stmt = $this->pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return false; 
        }

      
        if ($role === 'student' && $course_id) {
            $stmt = $this->pdo->prepare("SELECT status FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course || $course['status'] !== 'open') {
                return false; 
            }

            
            $year_level = (int)$year_level;
            if ($year_level < 1 || $year_level > 4) {
                return false;
            }
        } else {
            $course_id = null;
            $year_level = null;
        }

        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, password, role, course_id, year_level) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$username, $hashedPassword, $role, $course_id, $year_level]);
    }

    public function login($username, $password) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->username = $user['username'];
            $this->role = $user['role'];
            return $user;
        }
        return false;
    }
}
