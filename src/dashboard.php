<?php
require_once('./db/connection.php');
require_once('auth.php');




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['attendance_id'] ?? null;
    $student_id = $_POST['student_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];
    $marked_by = $_POST['marked_by'];

    if ($id) {
        $stmt = $pdo->prepare("UPDATE attendance SET student_id=?, date=?, time=?, status=?, marked_by=? WHERE id=?");
        $stmt->execute([$student_id, $date, $time, $status, $marked_by, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, time, status, marked_by, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$student_id, $date, $time, $status, $marked_by]);
    }

    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM attendance WHERE id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: dashboard.php");
    exit();
}


$stmt = $pdo->query("SELECT a.*, s.student_id, s.class, s.section, t.teacher_id 
                     FROM attendance a
                     LEFT JOIN students s ON a.student_id = s.id
                     LEFT JOIN teachers t ON a.marked_by = t.id
                     ORDER BY a.date DESC, a.time DESC");
$attendance = $stmt->fetchAll();


$students = $pdo->query("SELECT id, student_id, class, section FROM students")->fetchAll();
$teachers = $pdo->query("SELECT id, teacher_id FROM teachers")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background-color: #f8f9fa; }
    .main-content { margin-left: 250px; padding: 20px; }
    .table td, .table th { vertical-align: middle; text-align: center; }
    .badge { font-size: 0.9rem; }

 .navbar { padding: 1rem 2rem; }
    .navbar-brand { font-size: 1.5rem; }
    .form-control { width: auto; }
    .btn { margin-left: 0.5rem; }
    .text-white { display: flex; align-items: center; }
    .badge { margin-left: 0.5rem; }
    .nav-link { padding-top: 0.4rem; padding-bottom: 0.4rem; color: white; }
    .nav-link:hover { background-color: #f1f1f1; border-radius: 5px; color: black; }
    .btn i { margin-right: 5px; }
    .btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1; border-radius: 0.2rem; }
    .bg-primary { background-color: #4e73df !important; }
    .bg-light { background-color: #f8f9fa !important; }
    .bg-white { background-color: white !important; }
    .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important; }
    .rounded { border-radius: .25rem !important; }
    .main-content { overflow-y: auto; height: 100vh; }

    /* Table fixes */
    .table-responsive { overflow-x: auto; margin-top: 1rem; }
    .table th, .table td { white-space: nowrap; vertical-align: middle; text-align: center; }
    .table thead { background-color: #4e73df; color: white; }
    .table-striped > tbody > tr:nth-of-type(odd) { background-color: #f9f9f9; }
    .table-bordered { border: 1px solid #dee2e6; }
    .table tbody tr:hover { background-color: #dfe6f3; }
    .table { font-size: 0.9rem; }
</style>
</head>
<body>

<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>




<div class="main-content">
    <h2 class="mb-4"><i class="bi bi-clipboard-check"></i> Attendance Management</h2>

    <!-- Attendance Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-pencil-square"></i> Mark Attendance
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3" id="attendanceForm">
                <input type="hidden" name="attendance_id" id="attendance_id">

                <div class="col-md-3">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select" id="student_id" required>
                        <option value="">Select Student</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= $s['student_id'] ?> (<?= $s['class'] ?> - <?= $s['section'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Time</label>
                    <input type="time" name="time" id="time" class="form-control" value="<?= date('H:i:s') ?>" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" id="status" class="form-select" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Marked By</label>
                    <select name="marked_by" id="marked_by" class="form-select" required>
                        <option value="">Select Teacher</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['teacher_id'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-success" type="submit"><i class="bi bi-check-circle"></i> Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-table"></i> Attendance Records
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-bordered align-middle" id="attendanceTable">
                <thead class="table-secondary">
                    <tr>
                        <th>Student ID</th>
                        <th>Class</th>
                        <th>Section</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Marked By</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($attendance): ?>
                        <?php foreach($attendance as $a): ?>
                        <tr>
                            <td><?= $a['student_id'] ?></td>
                            <td><?= $a['class'] ?></td>
                            <td><?= $a['section'] ?></td>
                            <td><?= $a['date'] ?></td>
                            <td><?= $a['time'] ?></td>
                            <td>
                                <span class="badge <?= $a['status']=='present'?'bg-success':'bg-danger' ?>">
                                    <?= ucfirst($a['status']) ?>
                                </span>
                            </td>
                            <td><?= $a['teacher_id'] ?></td>
                            <td><?= $a['created_at'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm editBtn" 
                                    data-id="<?= $a['id'] ?>" 
                                    data-student="<?= $a['student_id'] ?>" 
                                    data-class="<?= $a['class'] ?>" 
                                    data-section="<?= $a['section'] ?>"
                                    data-date="<?= $a['date'] ?>" 
                                    data-time="<?= $a['time'] ?>" 
                                    data-status="<?= $a['status'] ?>" 
                                    data-marked="<?= $a['marked_by'] ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="dashboard.php?delete=<?= $a['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this record?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">No attendance records found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Update functionality
const editButtons = document.querySelectorAll('.editBtn');
editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('attendance_id').value = btn.dataset.id;
        document.getElementById('student_id').value = btn.dataset.student;
        document.getElementById('date').value = btn.dataset.date;
        document.getElementById('time').value = btn.dataset.time;
        document.getElementById('status').value = btn.dataset.status;
        document.getElementById('marked_by').value = btn.dataset.marked;

        window.scrollTo({ top: 0, behavior: 'smooth' }); // scroll to form
    });
});
</script>
</body>
</html>
