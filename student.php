<?php
session_start();
require_once "Core/dbconfig.php";
require_once "Classes/Student.php";
require_once "Classes/Layout.php";
require_once "Classes/ExcuseLetter.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "student") {
    header("Location: index(login).php"); 
    exit;
}

$student = new Student($pdo);
$student->id = $_SESSION['user']['id'];
$student->username = $_SESSION['user']['username'];

$excuseObj = new ExcuseLetter($pdo);

// Attendance submission
if (isset($_POST['fileAttendance'])) {
    $student->fileAttendance();
}

// Excuse letter submission
if (isset($_POST['submitExcuse'])) {
    $reason = trim($_POST['reason']);
    if (!empty($reason)) {
        $excuseObj->submitLetter($_SESSION['user']['id'], $_SESSION['user']['course_id'], $_SESSION['user']['year_level'], $reason);
    }
}

$history = $student->viewHistory();
$excuseLetters = $excuseObj->getStudentLetters($_SESSION['user']['id']);

echo Layout::header("Student Dashboard");
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

?>
    
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
<!-- Submit Excuse Letter -->
    <div class="col-md-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">Submit Excuse Letter</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <textarea name="reason" class="form-control" rows="3" placeholder="Enter your excuse..." required></textarea>
                    </div>
                    <button type="submit" name="submitExcuse" class="btn btn-warning">Submit Letter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Attendance History -->
<div class="card shadow-sm mb-4">
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
                        <tr><td colspan="5" class="text-center">No attendance records yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- My Excuse Letters -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">My Excuse Letters</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Date Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($excuseLetters)): ?>
                    <?php foreach ($excuseLetters as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['reason']); ?></td>
                            <td>
                                <span class="badge 
                                    <?= $row['status'] === 'Approved' ? 'bg-success' : 
                                       ($row['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning'); ?>">
                                    <?= htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['date_submitted']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="text-center">No excuse letters submitted.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
echo Layout::footer();