<?php
session_start();
require_once "Core/dbconfig.php";
require_once "Classes/User.php";
require_once "Classes/Course.php";

$user = new User($pdo);
$courseObj = new Course($pdo);

// Fetch only OPEN courses
$courses = $courseObj->getOpenCourses();

$error = "";
$success = ""; // success message holder

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];

    // Only set course/year for students
    $course_id = ($role == 'student') ? ($_POST['course_id'] ?? null) : null;
    $year_level = ($role == 'student') ? ($_POST['year_level'] ?? null) : null;

    if ($user->register($_POST['username'], $_POST['password'], $role, $course_id, $year_level)) {
        $success = "✅ Registered successfully! <a href='index(login).php' class='alert-link'>Login here</a>";
    } else {
        if ($role == "student" && $course_id) {
            // Check if the course was closed
            $stmt = $pdo->prepare("SELECT status FROM courses WHERE id = ?");
            $stmt->execute([$course_id]);
            $course = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$course) {
                $error = "❌ The selected course does not exist.";
            } elseif ($course['status'] !== 'open') {
                $error = "⚠️ This course is currently closed for enrollment.";
            } else {
                $error = "❌ Registration failed. Username might already be taken.";
            }
        } else {
            $error = "❌ Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow-lg p-4" style="width: 400px;">
        <h3 class="text-center mb-3">Register</h3>

        <!-- Success Alert -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required onchange="toggleStudentFields()">
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div id="studentFields">
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select">
                        <?php foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Year Level</label>
                    <select name="year_level" class="form-select">
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>

        <p class="mt-3 text-center">
            Already have an account? <a href="index(login).php">Login here</a>
        </p>
    </div>

    <script>
    function toggleStudentFields() {
        const role = document.getElementById("role").value;
        document.getElementById("studentFields").style.display = (role === "student") ? "block" : "none";
    }
    toggleStudentFields();
    </script>
</body>
</html>
