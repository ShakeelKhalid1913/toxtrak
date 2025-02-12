<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Fetch all clients for dropdown
$stmt = $pdo->query("SELECT * FROM clients ORDER BY first_name, last_name");
$clients = $stmt->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_POST['client_id'] ?? '';
    $total_weeks = $_POST['total_weeks'] ?? '';
    $total_appointments = $_POST['total_appointments'] ?? '';
    $lock_days = $_POST['lock_days'] ?? [];
    $appointment_type = $_POST['appointment_type'] ?? 'auto';

    // Validate inputs
    if (empty($client_id)) {
        $errors[] = "Please select a client";
    }
    if ($total_weeks < 8 || $total_weeks > 52) {
        $errors[] = "Total weeks must be between 8 and 52";
    }
    if ($total_appointments < 8 || $total_appointments > ($total_weeks * 4)) {
        $errors[] = "Total appointments must be between 8 and " . ($total_weeks * 4);
    }
    
    if (empty($errors)) {
        // Process appointment creation
        // ... (existing appointment creation code)
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Appointment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .card {
            background-color: #2a2a2a !important;
            border: 1px solid #3a3a3a;
        }
        .card-body {
            padding: 1rem;
        }
        .preview-card {
            background: linear-gradient(145deg, #2a2a2a, #333333);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .preview-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .preview-card .card-header {
            background: linear-gradient(145deg, #333333, #2a2a2a);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px 12px 0 0;
            padding: 15px;
        }
        .preview-card .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            color: #fff;
            opacity: 0.9;
        }
        .preview-card .card-body {
            padding: 15px;
        }
        .appointment-day {
            display: flex;
            align-items: center;
            margin: 8px 0;
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .appointment-day:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .appointment-day .day-dot {
            width: 8px;
            height: 8px;
            background: #0d6efd;
            border-radius: 50%;
            margin-right: 10px;
        }
        .appointment-day .day-text {
            color: #fff;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        .preview-section {
            margin-top: 30px;
        }
        .preview-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #fff;
            opacity: 0.9;
        }
        .preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-4">
        <h2>Create Appointment</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form id="appointmentForm" method="POST" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="client_id" class="form-label">Select User:</label>
                    <select class="form-select" id="client_id" name="client_id" required>
                        <option value="">Select User</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>">
                                <?= htmlspecialchars($client['first_name'] . ' ' . $client['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lock Days (days to exclude):</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Mon" id="monday">
                            <label class="form-check-label" for="monday">Mon</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Tue" id="tuesday">
                            <label class="form-check-label" for="tuesday">Tue</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Wed" id="wednesday">
                            <label class="form-check-label" for="wednesday">Wed</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Thu" id="thursday">
                            <label class="form-check-label" for="thursday">Thu</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Fri" id="friday">
                            <label class="form-check-label" for="friday">Fri</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Sat" id="saturday">
                            <label class="form-check-label" for="saturday">Sat</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lock_days[]" value="Sun" id="sunday">
                            <label class="form-check-label" for="sunday">Sun</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="total_weeks" class="form-label">Total Weeks (min:8 max:52):</label>
                    <input type="number" class="form-control" id="total_weeks" name="total_weeks" min="8" max="52" required>
                </div>
                <div class="col-md-6">
                    <label for="total_appointments" class="form-label" id="total_appointments_label">Total Appointments:</label>
                    <input type="number" class="form-control" id="total_appointments" name="total_appointments" required>
                </div>
            </div>

            <button type="button" id="previewBtn" class="btn btn-secondary">Create Preview</button>
            <button type="submit" class="btn btn-primary d-none" id="submitBtn">Submit</button>
        </form>

        <!-- Preview Section -->
        <div id="previewSection" class="preview-section mt-4 d-none">
            <h2 class="preview-title">Appointment Preview</h2>
            <div id="weeklyPreview" class="preview-grid">
                <!-- Weekly previews will be inserted here -->
            </div>
            <div class="text-end mt-4">
                <button type="button" class="btn btn-danger me-2" id="cancelPreviewBtn">Cancel</button>
                <button type="button" class="btn btn-success" id="saveAppointmentBtn">Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const appointmentForm = document.getElementById('appointmentForm');
        const previewBtn = document.getElementById('previewBtn');
        const submitBtn = document.getElementById('submitBtn');
        const previewSection = document.getElementById('previewSection');
        const weeklyPreview = document.getElementById('weeklyPreview');
        const cancelPreviewBtn = document.getElementById('cancelPreviewBtn');
        const saveAppointmentBtn = document.getElementById('saveAppointmentBtn');
        const totalWeeksInput = document.getElementById('total_weeks');
        const totalAppointmentsInput = document.getElementById('total_appointments');
        const totalAppointmentsLabel = document.getElementById('total_appointments_label');
        const lockedDaysCheckboxes = document.querySelectorAll('input[name="lock_days[]"]');

        // Update constraints when inputs change
        [totalWeeksInput].forEach(input => {
            input.addEventListener('input', updateConstraints);
        });

        // Update constraints when lock days change
        lockedDaysCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateConstraints);
        });

        function updateConstraints() {
            const totalWeeks = parseInt(totalWeeksInput.value) || 0;
            const lockedDays = Array.from(lockedDaysCheckboxes).filter(cb => cb.checked).length;
            const availableDays = 7 - lockedDays;
            const MAX_APPOINTMENTS_PER_WEEK = 4;

            // Calculate max appointments per week based on available days
            // If available days are less than max appointments, use available days
            // Otherwise use max appointments (4)
            const maxAppointmentsPerWeek = availableDays < MAX_APPOINTMENTS_PER_WEEK 
                ? availableDays 
                : MAX_APPOINTMENTS_PER_WEEK;

            // Update total appointments constraints
            const minTotalAppointments = totalWeeks; // At least 1 per week
            const maxTotalAppointments = totalWeeks * maxAppointmentsPerWeek;

            totalAppointmentsInput.min = minTotalAppointments;
            totalAppointmentsInput.max = maxTotalAppointments;

            // Update help text
            totalAppointmentsLabel.textContent = `Total Appointments (min:${minTotalAppointments} max:${maxTotalAppointments}):`;

            // Adjust current value if outside new range
            if (totalAppointmentsInput.value > maxTotalAppointments) {
                totalAppointmentsInput.value = maxTotalAppointments;
            } else if (totalAppointmentsInput.value < minTotalAppointments) {
                totalAppointmentsInput.value = minTotalAppointments;
            }
        }

        previewBtn.addEventListener('click', function() {
            if (!appointmentForm.checkValidity()) {
                appointmentForm.reportValidity();
                return;
            }

            const totalWeeks = parseInt(totalWeeksInput.value);
            const totalAppointments = parseInt(totalAppointmentsInput.value);
            
            // Calculate next Monday
            const today = new Date();
            const nextMonday = new Date(today);
            nextMonday.setDate(today.getDate() + (8 - today.getDay()) % 7);
            // If today is Monday, move to next week's Monday
            if (today.getDay() === 1) {
                nextMonday.setDate(nextMonday.getDate() + 7);
            }
            
            const lockedDays = Array.from(lockedDaysCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const availableDays = 7 - lockedDays.length;
            const MAX_APPOINTMENTS_PER_WEEK = 4;
            const maxAppointmentsPerWeek = availableDays < MAX_APPOINTMENTS_PER_WEEK 
                ? availableDays 
                : MAX_APPOINTMENTS_PER_WEEK;
            
            // Calculate minimum appointments per week to distribute evenly
            const minPerWeek = Math.floor(totalAppointments / totalWeeks);
            let extras = totalAppointments % totalWeeks;
            
            // Create array of appointments per week
            let appointmentsPerWeek = new Array(totalWeeks).fill(minPerWeek);
            
            // Distribute extra appointments randomly
            while (extras > 0) {
                const randomWeek = Math.floor(Math.random() * totalWeeks);
                if (appointmentsPerWeek[randomWeek] < maxAppointmentsPerWeek) {
                    appointmentsPerWeek[randomWeek]++;
                    extras--;
                }
            }

            let previewHtml = '';
            let currentDate = new Date(nextMonday); // Start from next Monday
            const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            let totalAssigned = 0;

            // Generate preview for each week
            for (let week = 0; week < totalWeeks; week++) {
                const weekStart = new Date(currentDate);
                const weekEnd = new Date(currentDate);
                weekEnd.setDate(weekEnd.getDate() + 6);

                const weekAppointments = appointmentsPerWeek[week];
                totalAssigned += weekAppointments;

                // Generate appointments for this week
                let weekDays = [];
                const availableDays = days.filter(day => !lockedDays.includes(day));
                
                for (let i = 0; i < weekAppointments; i++) {
                    if (availableDays.length === 0) break;
                    const randomIndex = Math.floor(Math.random() * availableDays.length);
                    weekDays.push(availableDays[randomIndex]);
                    availableDays.splice(randomIndex, 1);
                }

                // Sort appointments by day order
                weekDays.sort((a, b) => days.indexOf(a) - days.indexOf(b));

                previewHtml += `
                    <div class="preview-card">
                        <div class="card-header">
                            <h6 class="card-title">${formatDate(weekStart)} - ${formatDate(weekEnd)}</h6>
                        </div>
                        <div class="card-body">
                            ${weekDays.map(day => `
                                <div class="appointment-day">
                                    <div class="day-dot"></div>
                                    <span class="day-text">${day}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>`;

                currentDate.setDate(currentDate.getDate() + 7);
            }

            // Verify total appointments
            if (totalAssigned !== totalAppointments) {
                console.error(`Appointment mismatch! Expected: ${totalAppointments}, Got: ${totalAssigned}`);
                return;
            }

            previewSection.innerHTML = `
                <h2 class="preview-title">Appointment Preview</h2>
                <div class="preview-grid">
                    ${previewHtml}
                </div>
                <div class="text-end mt-4">
                    <button type="button" class="btn btn-danger me-2" id="cancelPreviewBtn">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveAppointmentBtn">Confirm</button>
                </div>
            `;
            
            // Add event listener for the cancel button
            document.getElementById('cancelPreviewBtn').addEventListener('click', () => {
                previewSection.classList.add('d-none');
                previewBtn.classList.remove('d-none');
            });

            // Add event listener for the save button
            document.getElementById('saveAppointmentBtn').addEventListener('click', async () => {
                const weeklyAppointments = [];
                document.querySelectorAll('.preview-card').forEach(card => {
                    const dateRange = card.querySelector('.card-title').textContent.split(' - ');
                    const days = Array.from(card.querySelectorAll('.day-text')).map(day => day.textContent);
                    
                    weeklyAppointments.push({
                        start_date: formatDateForDB(new Date(dateRange[0])),
                        end_date: formatDateForDB(new Date(dateRange[1])),
                        days: days
                    });
                });

                const formData = {
                    client_id: document.getElementById('client_id').value,
                    total_weeks: parseInt(totalWeeksInput.value),
                    total_appointments: parseInt(totalAppointmentsInput.value),
                    weekly_appointments: weeklyAppointments
                };

                try {
                    const saveBtn = document.getElementById('saveAppointmentBtn');
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

                    const response = await fetch('save_appointment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Appointment created successfully!');
                        window.location.href = 'appointments.php';
                    } else {
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Confirm';
                        throw new Error(result.message);
                    }
                } catch (error) {
                    alert('Error: ' + error.message);
                }
            });

            previewSection.classList.remove('d-none');
            previewBtn.classList.add('d-none');
        });

        cancelPreviewBtn.addEventListener('click', function() {
            previewSection.classList.add('d-none');
            previewBtn.classList.remove('d-none');
        });

        appointmentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
        });

        function formatDate(date) {
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric' 
            });
        }

        function formatDateForDB(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
    });
    </script>
</body>
</html>
