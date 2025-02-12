<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        body {
            background-color: #2b2b2b;
            min-height: 100vh;
            color: #fff;
        }
        .navbar {
            background-color: #2b2b2b !important;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand img {
            height: 60px;
        }
        .nav-link {
            color: #fff !important;
            padding: 0.8rem 1.5rem !important;
            border-radius: 6px;
            margin: 0 0.3rem;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
        }
        .nav-link.active {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
        }
        .btn-logout {
            background-color: #dc3545;
            color: #fff;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn-logout:hover {
            background-color: #bb2d3b;
            color: #fff;
        }
        .dashboard-container {
            padding: 2rem;
        }
        .alert-success {
            background: linear-gradient(45deg, #00d2ff20, #3a7bd520);
            border: 1px solid #00d2ff40;
            color: #fff;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="dashboard-container container-fluid mt-4">
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill"></i> You have successfully logged in!
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h2>Welcome to Dashboard</h2>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
