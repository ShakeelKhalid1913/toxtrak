<?php
// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="logo.png" alt="ToxTrak Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'clients.php' ? 'active' : '' ?>" href="clients.php">
                        <i class="bi bi-people"></i> Clients
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'appointments.php' ? 'active' : '' ?>" href="appointments.php">
                        <i class="bi bi-calendar-check"></i> Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $current_page === 'report.php' ? 'active' : '' ?>" href="report.php">
                        <i class="bi bi-file-text"></i> Reports
                    </a>
                </li>
            </ul>
            <a href="logout.php" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>
