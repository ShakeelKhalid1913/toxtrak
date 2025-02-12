<?php
session_start();
require_once 'db.php';

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $dob = $_POST['date_of_birth'];
    $phone = trim($_POST['phone']);
    $emergency_phone = trim($_POST['emergency_phone']);
    $address = trim($_POST['address'] ?? '');  // Optional
    $note = trim($_POST['note'] ?? '');        // Optional

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($gender) || empty($dob) || empty($phone) || empty($emergency_phone)) {
        $error = "All required fields must be filled out.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";   
    } else {
        try {
            $sql = "INSERT INTO clients (first_name, last_name, email, gender, date_of_birth, 
                    phone_number, emergency_phone_number, address, note) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $first_name, $last_name, $email, $gender, $dob, 
                $phone, $emergency_phone, $address, $note
            ]);

            header("Location: clients.php");
            exit();
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'clients_email_unique') !== false) {
                $error = "This email address is already registered.";
            } else {
                $error = "Error adding client: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Add Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Add New Client</h2>
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
                       value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>" required>
            </div>

            <div class="col-md-6">
                <label for="last_name" class="form-label">Last Name*</label>
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Email*</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
            </div>

            <div class="col-md-6">
                <label for="gender" class="form-label">Gender*</label>
                <select class="form-select" id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?= isset($_POST['gender']) && $_POST['gender'] === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= isset($_POST['gender']) && $_POST['gender'] === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= isset($_POST['gender']) && $_POST['gender'] === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="date_of_birth" class="form-label">Date of Birth*</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                       value="<?= isset($_POST['date_of_birth']) ? htmlspecialchars($_POST['date_of_birth']) : '' ?>" required>
            </div>

            <div class="col-md-4">
                <label for="phone" class="form-label">Phone Number*</label>
                <input type="tel" class="form-control" id="phone" name="phone" 
                       value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>" required>
            </div>

            <div class="col-md-4">
                <label for="emergency_phone" class="form-label">Emergency Phone*</label>
                <input type="tel" class="form-control" id="emergency_phone" name="emergency_phone" 
                       value="<?= isset($_POST['emergency_phone']) ? htmlspecialchars($_POST['emergency_phone']) : '' ?>" required>
            </div>

            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>
            </div>

            <div class="col-12">
                <label for="note" class="form-label">Note</label>
                <textarea class="form-control" id="note" name="note" rows="3"><?= isset($_POST['note']) ? htmlspecialchars($_POST['note']) : '' ?></textarea>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">Add Client</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
