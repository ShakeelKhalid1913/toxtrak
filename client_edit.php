<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Get client ID from URL
if (!isset($_GET['id'])) {
    header("Location: clients.php");
    exit();
}

$client_id = $_GET['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $phone = trim($_POST['phone_number']);
    $emergency_phone = trim($_POST['emergency_phone_number']);
    $address = trim($_POST['address'] ?? '');  // Optional
    $note = trim($_POST['note'] ?? '');        // Optional

    try {
        $sql = "UPDATE clients SET 
                first_name = ?, last_name = ?, email = ?, gender = ?, 
                date_of_birth = ?, phone_number = ?, emergency_phone_number = ?, 
                address = ?, note = ? 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $first_name, $last_name, $email, $gender, $dob,
            $phone, $emergency_phone, $address, $note, $client_id
        ]);

        header("Location: clients.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error updating client: " . $e->getMessage();
    }
}

// Get client details
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

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
    <title>ToxTrak - Edit Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Client</h2>
            <a href="clients.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Clients
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="" class="row g-3">
            <div class="col-md-6">
                <label for="first_name" class="form-label">First Name*</label>
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       value="<?= htmlspecialchars($client['first_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?= htmlspecialchars($client['last_name']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email*</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>

            <div class="col-md-6">
                <label for="gender" class="form-label">Gender*</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?= $client['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= $client['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= $client['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="date_of_birth" class="form-label">Date of Birth*</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                       value="<?= htmlspecialchars($client['date_of_birth']) ?>" required>
            </div>

            <div class="col-md-4">
                <label for="phone_number" class="form-label">Phone Number*</label>
                <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                       value="<?= htmlspecialchars($client['phone_number']) ?>" required>
            </div>

            <div class="col-md-4">
                <label for="emergency_phone_number" class="form-label">Emergency Phone*</label>
                <input type="tel" class="form-control" id="emergency_phone_number" name="emergency_phone_number" 
                       value="<?= htmlspecialchars($client['emergency_phone_number']) ?>" required>
            </div>

            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($client['address']) ?></textarea>
            </div>

            <div class="col-12">
                <label for="note" class="form-label">Notes</label>
                <textarea class="form-control" id="note" name="note" rows="3"><?= htmlspecialchars($client['note']) ?></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Update Client</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
