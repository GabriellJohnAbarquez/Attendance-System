<?php
session_start();
require_once "Core/dbconfig.php";
require_once "Classes/Student.php";
require_once "Classes/Layout.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "student") {
    header("Location: index(login).php"); 
    exit;
}

$student = new Student($pdo);
$student->id = $_SESSION['user']['id'];
$student->username = $_SESSION['user']['username'];

if (isset($_POST['fileAttendance'])) {
    $student->fileAttendance();
}

$history = $student->viewHistory();

echo Layout::header("Student Dashboard");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['username']); ?> (Student)</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="row">
    <!-- File Attendance -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">File Attendance</h5>
            </div>
            <div class="card-body text-center">
                <form method="POST">
                    <button type="submit" name="fileAttendance" class="btn btn-success btn-lg">
                        Submit Attendance
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Attendance History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Time In</th>
                                <th>Status</th>
                                <th>Course</th>
                                <th>Year Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($history)): ?>
                                <?php foreach ($history as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['date']); ?></td>
                                        <td><?= htmlspecialchars($row['time_in']); ?></td>
                                        <td>
                                            <span class="badge <?= $row['status'] === 'Late' ? 'bg-danger' : 'bg-success'; ?>">
                                                <?= htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($row['course_name'] ?? 'N/A'); ?></td>
                                        <td><?= htmlspecialchars($row['year_level'] ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No attendance records yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo Layout::footer();
