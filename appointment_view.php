<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Check if appointment ID is provided
if (!isset($_GET['id'])) {
    header("Location: appointments.php");
    exit();
}

$appointment_id = $_GET['id'];

// Get appointment details with client information
$sql = "SELECT a.*, c.first_name, c.last_name, c.email, c.phone_number,
        COUNT(ad.id) as completed_appointments
        FROM appointments a
        JOIN clients c ON a.client_id = c.id
        LEFT JOIN appointment_details ad ON a.id = ad.appointment_id
        WHERE a.id = :id
        GROUP BY a.id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $appointment_id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    header("Location: appointments.php");
    exit();
}

// Get weekly schedule
$sql = "SELECT * FROM appointment_details 
        WHERE appointment_id = :appointment_id 
        ORDER BY week_start_date";
$stmt = $pdo->prepare($sql);
$stmt->execute(['appointment_id' => $appointment_id]);
$weekly_schedule = $stmt->fetchAll();

// Handle appointment deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    try {
        $pdo->beginTransaction();
        
        // Delete appointment details first (due to foreign key constraint)
        $sql = "DELETE FROM appointment_details WHERE appointment_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $appointment_id]);
        
        // Then delete the appointment
        $sql = "DELETE FROM appointments WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $appointment_id]);
        
        $pdo->commit();
        header("Location: appointments.php");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error deleting appointment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - View Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Client Information</h4>
                    </div>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></h5>
                        <p class="mb-1">
                            <i class="bi bi-envelope"></i> 
                            <?= htmlspecialchars($appointment['email']) ?>
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-telephone"></i> 
                            <?= htmlspecialchars($appointment['phone_number']) ?>
                        </p>
                        <hr>
                        <p class="mb-1">
                            <strong>Type:</strong> 
                            <span class="badge <?= $appointment['appointment_type'] === 'auto' ? 'bg-success' : 'bg-warning' ?>">
                                <?= ucfirst($appointment['appointment_type']) ?>
                            </span>
                        </p>
                        <p class="mb-1">
                            <strong>Total Weeks:</strong> 
                            <?= $appointment['total_appointment_weeks'] ?>
                        </p>
                        <hr>
                        <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                            <div class="d-grid gap-2">
                                <button type="submit" name="delete" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete Appointment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">Weekly Schedule</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Week</th>
                                        <th>Mon</th>
                                        <th>Tue</th>
                                        <th>Wed</th>
                                        <th>Thu</th>
                                        <th>Fri</th>
                                        <th>Sat</th>
                                        <th>Sun</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($weekly_schedule as $week): ?>
                                        <tr>
                                            <td>
                                                <?= date('M j', strtotime($week['week_start_date'])) ?> - 
                                                <?= date('M j', strtotime($week['week_end_date'])) ?>
                                            </td>
                                            <td><?= $week['monday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['tuesday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['wednesday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['thursday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['friday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['saturday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                            <td><?= $week['sunday_appointment'] ? '<i class="bi bi-check-circle-fill text-success"></i>' : '' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
