<?php
session_start();
require_once 'db.php';
require_once 'vendor/autoload.php'; // For TCPDF

// Check if user is not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Fetch all clients for dropdown
$stmt = $pdo->query("SELECT id, first_name, last_name FROM clients ORDER BY first_name, last_name");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get appointments for a client within date range
function getAppointments($pdo, $client_id, $start_date, $end_date) {
    $params = [$start_date, $end_date];
    $clientCondition = "";
    
    if ($client_id !== 'all') {
        $clientCondition = "AND a.client_id = ?";
        $params[] = $client_id;
    }
    
    $sql = "SELECT ad.*, 
            c.first_name, 
            c.last_name,
            a.client_id
            FROM appointment_details ad 
            JOIN appointments a ON ad.appointment_id = a.id 
            JOIN clients c ON a.client_id = c.id
            WHERE ad.week_start_date >= ? 
            AND ad.week_start_date <= ?
            $clientCondition
            ORDER BY c.first_name, c.last_name, ad.week_start_date ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Generate PDF report
function generatePDF($appointments, $client_name, $start_date, $end_date) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('ToxTrak');
    $pdf->SetAuthor('ToxTrak System');
    $pdf->SetTitle('Appointment Report');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add a page
    $pdf->AddPage('L'); // Landscape mode for more space
    
    // Set font
    $pdf->SetFont('helvetica', '', 12);
    
    // Add title
    $pdf->Cell(0, 10, 'Appointment Report', 0, 1, 'C');
    $pdf->Cell(0, 10, $client_name === 'All Users' ? "All Users" : "Client: $client_name", 0, 1, 'L');
    $pdf->Cell(0, 10, "Period: $start_date to $end_date", 0, 1, 'L');
    $pdf->Ln(5);
    
    // Create table header
    $header = array('Client Name', 'Week Period', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');
    
    // Calculate column widths
    $w = array(60, 60, 20, 20, 20, 20, 20, 20, 20);
    
    // Set fill color for header
    $pdf->SetFillColor(240, 240, 240);
    
    // Header
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Set font for checkmark
    $pdf->SetFont('zapfdingbats', '', 10);
    $checkmark = "4"; // Zapfdingbats checkmark character
    
    // Data
    $currentClient = '';
    foreach($appointments as $row) {
        // Switch back to regular font for text
        $pdf->SetFont('helvetica', '', 10);
        
        $clientName = $row['first_name'] . ' ' . $row['last_name'];
        // Only show client name if it's different from the previous row
        $displayName = ($clientName !== $currentClient) ? $clientName : '';
        $currentClient = $clientName;
        
        $pdf->Cell($w[0], 6, $displayName, 1, 0, 'L');
        $pdf->Cell($w[1], 6, $row['week_start_date'] . ' to ' . $row['week_end_date'], 1, 0, 'L');
        
        // Switch to zapfdingbats for checkmarks
        $pdf->SetFont('zapfdingbats', '', 10);
        $pdf->Cell($w[2], 6, $row['monday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[3], 6, $row['tuesday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[4], 6, $row['wednesday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[5], 6, $row['thursday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[6], 6, $row['friday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[7], 6, $row['saturday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Cell($w[8], 6, $row['sunday_appointment'] ? $checkmark : '', 1, 0, 'C');
        $pdf->Ln();
    }
    
    return $pdf->Output('appointment_report.pdf', 'S');
}

$appointments = [];
$selected_client_name = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Get client name
    if ($client_id === 'all') {
        $selected_client_name = 'All Users';
    } else {
        foreach ($clients as $client) {
            if ($client['id'] == $client_id) {
                $selected_client_name = $client['first_name'] . ' ' . $client['last_name'];
                break;
            }
        }
    }
    
    // Get appointments
    $appointments = getAppointments($pdo, $client_id, $start_date, $end_date);
    
    // Handle PDF download
    if (isset($_POST['download_pdf'])) {
        $pdf_content = generatePDF($appointments, $selected_client_name, $start_date, $end_date);
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="appointment_report.pdf"');
        echo $pdf_content;
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToxTrak - Generate Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }
        .check-mark {
            color: #28a745;
            font-size: 1.2em;
        }
        .client-row {
            background-color: #f8f9fa;
        }
        .client-name {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid mt-4">
        <h2 class="mb-4">Generate Report</h2>

        <form method="POST" action="" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="start_date" class="form-label">Start Date:</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?= isset($_POST['start_date']) ? $_POST['start_date'] : '' ?>" required>
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">End Date:</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?= isset($_POST['end_date']) ? $_POST['end_date'] : '' ?>" required>
            </div>

            <div class="col-md-4">
                <label for="client_id" class="form-label">Select User:</label>
                <select class="form-select" id="client_id" name="client_id" required>
                    <option value="">Select User</option>
                    <option value="all" <?= isset($_POST['client_id']) && $_POST['client_id'] == 'all' ? 'selected' : '' ?>>All Users</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= isset($_POST['client_id']) && $_POST['client_id'] == $client['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">Generate</button>
            </div>
        </form>

        <?php if (!empty($appointments)): ?>
            <div class="mb-3">
                <form method="POST" action="" class="d-inline">
                    <input type="hidden" name="client_id" value="<?= htmlspecialchars($_POST['client_id']) ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($_POST['start_date']) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($_POST['end_date']) ?>">
                    <button type="submit" name="download_pdf" class="btn btn-secondary">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Week Period</th>
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
                        <?php 
                        $currentClient = '';
                        foreach ($appointments as $appointment): 
                            $clientName = $appointment['first_name'] . ' ' . $appointment['last_name'];
                            $newClient = $clientName !== $currentClient;
                            $currentClient = $clientName;
                        ?>
                            <tr <?= $newClient ? 'class="client-row"' : '' ?>>
                                <td class="text-start">
                                    <?= $newClient ? "<span class='client-name'>" . htmlspecialchars($clientName) . "</span>" : "" ?>
                                </td>
                                <td class="text-start"><?= $appointment['week_start_date'] ?> to <?= $appointment['week_end_date'] ?></td>
                                <td><?= $appointment['monday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['tuesday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['wednesday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['thursday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['friday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['saturday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                                <td><?= $appointment['sunday_appointment'] ? '<i class="bi bi-check-lg check-mark"></i>' : '' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add date validation
        document.addEventListener('DOMContentLoaded', function() {
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            function validateDates() {
                if (startDate.value && endDate.value) {
                    if (startDate.value > endDate.value) {
                        alert('End date must be after start date');
                        endDate.value = '';
                    }
                }
            }
            
            startDate.addEventListener('change', validateDates);
            endDate.addEventListener('change', validateDates);
        });
    </script>
</body>
</html>
