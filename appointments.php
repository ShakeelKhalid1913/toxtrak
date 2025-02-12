<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Get search query if any
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Modify the SQL query to include search
$sql = "SELECT a.*, 
        c.first_name, c.last_name, c.email, c.phone_number,
        COUNT(ad.id) as total_appointments
        FROM appointments a
        LEFT JOIN clients c ON a.client_id = c.id
        LEFT JOIN appointment_details ad ON a.id = ad.appointment_id
        WHERE 1=1";

if (!empty($search)) {
    $search = "%$search%";
    $sql .= " AND (c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR c.phone_number LIKE ? OR a.appointment_type LIKE ?)";
}

$sql .= " GROUP BY a.id, a.client_id, a.appointment_type, a.total_appointments, a.created_at, 
          c.first_name, c.last_name, c.email, c.phone_number 
          ORDER BY a.created_at DESC";

$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $stmt->execute([$search, $search, $search, $search, $search]);
} else {
    $stmt->execute();
}
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Appointments</h2>
            <a href="appointment_create.php" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> New Appointment
            </a>
        </div>

        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by client name, email, phone, or appointment type..." 
                       name="search" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="appointments.php" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Contact</th>
                        <th>Type</th>
                        <!-- <th>Progress</th> -->
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?></td>
                        <td>
                            <div><?= htmlspecialchars($appointment['email']) ?></div>
                            <div class="text-muted"><?= htmlspecialchars($appointment['phone_number']) ?></div>
                        </td>
                        <td>
                            <span class="badge <?= $appointment['appointment_type'] === 'auto' ? 'bg-success' : 'bg-warning' ?>">
                                <?= ucfirst($appointment['appointment_type']) ?>
                            </span>
                        </td>
                        <!-- <td>
                            <?php 
                            $progress = ($appointment['completed_appointments'] / $appointment['total_appointments']) * 100;
                            ?>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: <?= $progress ?>%"
                                     aria-valuenow="<?= $progress ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <?= round($progress) ?>%
                                </div>
                            </div>
                            <small class="text-muted">
                                <?= $appointment['completed_appointments'] ?>/<?= $appointment['total_appointments'] ?> appointments
                            </small>
                        </td> -->
                        <td><?= date('M j, Y', strtotime($appointment['created_at'])) ?></td>
                        <td>
                            <a href="appointment_view.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
