<?php
session_start();
$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if (empty($email)) {
        $error = "Please login to access the dashboard.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2b2b2b;
            min-height: 100vh;
        }
        .navbar {
            background-color: #2b2b2b !important;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-brand img {
            height: 60px;
        }
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 0 20px;
        }
        .card {
            background-color: #333;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
        }
        .card-body {
            padding: 2rem;
        }
        .form-label {
            color: #fff;
        }
        .form-control {
            background-color: #444;
            border: 1px solid #555;
            color: #fff;
            padding: 0.8rem;
        }
        .form-control:focus {
            background-color: #4a4a4a;
            border-color: #00d2ff;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0, 210, 255, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #00d2ff, #3a7bd5);
            border: none;
            padding: 0.8rem;
            width: 100%;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #00bfe6, #3373c4);
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.2);
            color: #ff6b6b;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="ToxTrak Logo">
            </a>
        </div>
    </nav>

    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="validate.php">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               placeholder="Enter your email">
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                               placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
