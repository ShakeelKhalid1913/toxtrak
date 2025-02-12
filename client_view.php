<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Get client ID from URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: clients.php");
    exit();
}

// Fetch client details
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    header("Location: clients.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Client Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Client Details</h2>
            <a href="clients.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Clients
            </a>
        </div>

        <div class="client-details">
            <div class="detail-row">
                <div class="detail-label">Name</div>
                <div class="detail-value">
                    <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Email</div>
                <div class="detail-value"><?= htmlspecialchars($client['email']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Phone</div>
                <div class="detail-value"><?= htmlspecialchars($client['phone_number']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Emergency Phone</div>
                <div class="detail-value"><?= htmlspecialchars($client['emergency_phone_number']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Gender</div>
                <div class="detail-value"><?= htmlspecialchars($client['gender']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Date of birth</div>
                <div class="detail-value"><?= htmlspecialchars($client['date_of_birth']) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Address</div>
                <div class="detail-value"><?= nl2br(htmlspecialchars($client['address'])) ?></div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Note</div>
                <div class="detail-value"><?= nl2br(htmlspecialchars($client['note'])) ?></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
