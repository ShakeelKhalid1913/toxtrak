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
$sql = "SELECT * FROM clients WHERE 1=1";
if (!empty($search)) {
    $search = "%$search%";
    $sql .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
}
$sql .= " ORDER BY id ASC";

$stmt = $pdo->prepare($sql);
if (!empty($search)) {
    $stmt->execute([$search, $search, $search, $search]);
} else {
    $stmt->execute();
}
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Clients</h2>
            <a href="client_add.php" class="btn btn-add">
                <i class="bi bi-plus-lg"></i> Add New Client
            </a>
        </div>

        <!-- Add search form -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search by name, email, or phone..." 
                       name="search" value="<?= htmlspecialchars($search) ?>">
                <button class="btn btn-outline-secondary" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="clients.php" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Phone#</th>
                        <th scope="col">Emergency#</th>
                        <th scope="col" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clients as $i => $client): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?></td>
                        <td><?= htmlspecialchars($client['email']) ?></td>
                        <td><?= htmlspecialchars($client['gender']) ?></td>
                        <td><?= htmlspecialchars($client['phone_number']) ?></td>
                        <td><?= htmlspecialchars($client['emergency_phone_number']) ?></td>
                        <td class="text-end">
                            <a href="client_view.php?id=<?= $client['id'] ?>" class="action-btn btn-view" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="client_edit.php?id=<?= $client['id'] ?>" class="action-btn btn-edit" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="client_delete.php?id=<?= $client['id'] ?>" class="action-btn btn-delete" 
                               onclick="return confirm('Are you sure you want to delete this client?')" title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
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
