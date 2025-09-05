<?php
session_start();
require_once "Core/dbconfig.php";
require_once "Classes/Admin.php";
require_once "Classes/Course.php";
require_once "Classes/Layout.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== "admin") {
    header("Location: index(login).php"); 
    exit;
}

$admin = new Admin($pdo);
$courseObj = new Course($pdo);

// Add new course
if (isset($_POST['addCourse']) && !empty($_POST['course_name'])) {
    $courseObj->add($_POST['course_name']);
}

// Update course status
if (isset($_POST['updateStatus'])) {
    $courseObj->updateStatus($_POST['course_id'], $_POST['status']);
}

// Delete course
if (isset($_POST['deleteCourse'])) {
    $courseObj->delete($_POST['course_id']);
}

$courses = $courseObj->getAll();
$attendanceReport = [];

if (isset($_POST['viewReport'])) {
    $attendanceReport = $admin->viewAttendanceByCourseYear($_POST['course_id'], $_POST['year_level']);
}

echo Layout::header("Admin Dashboard");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['username']); ?> (Admin)</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<!-- Add Course -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Add New Course</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
            </div>
            <div class="col-md-4">
                <button type="submit" name="addCourse" class="btn btn-success w-100">Add Course</button>
            </div>
        </form>
    </div>
</div>

<!-- All Courses -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">Manage Courses</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['course_name']); ?></td>
                        <td>
                            <span class="badge <?= $c['status'] === 'open' ? 'bg-success' : 'bg-danger'; ?>">
                                <?= ucfirst($c['status']); ?>
                            </span>
                        </td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="course_id" value="<?= $c['id']; ?>">
                                <input type="hidden" name="status" value="<?= $c['status'] === 'open' ? 'closed' : 'open'; ?>">
                                <button type="submit" name="updateStatus" class="btn btn-sm btn-warning">
                                    <?= $c['status'] === 'open' ? 'Close Enrollment' : 'Open Enrollment'; ?>
                                </button>
                            </form>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                <input type="hidden" name="course_id" value="<?= $c['id']; ?>">
                                <button type="submit" name="deleteCourse" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Attendance Report -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">View Attendance by Course & Year</h5>
    </div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-5">
                <select name="course_id" class="form-select" required>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['course_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <select name="year_level" class="form-select" required>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" name="viewReport" class="btn btn-primary w-100">Generate</button>
            </div>
        </form>

        <?php if (!empty($attendanceReport)): ?>
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Time In</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceReport as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= htmlspecialchars($row['date']); ?></td>
                                <td><?= htmlspecialchars($row['time_in']); ?></td>
                                <td>
                                    <span class="badge <?= $row['status'] === 'Late' ? 'bg-danger' : 'bg-success'; ?>">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (isset($_POST['viewReport'])): ?>
            <p class="text-center mt-3">No attendance records found.</p>
        <?php endif; ?>
    </div>
</div>

<?php
echo Layout::footer();
