<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // Extract main appointment data
    $clientId = $data['client_id'] ?? null;
    $totalWeeks = $data['total_weeks'] ?? null;
    $totalAppointments = $data['total_appointments'] ?? null;
    $weeklyAppointments = $data['weekly_appointments'] ?? null;

    // Validate required fields
    if (!$clientId || !$totalWeeks || !$totalAppointments || !$weeklyAppointments) {
        throw new Exception('Missing required fields');
    }

    // Check if client already has an appointment
    $stmt = $pdo->prepare("SELECT id FROM appointments WHERE client_id = :client_id");
    $stmt->execute([':client_id' => $clientId]);
    
    if ($stmt->fetch()) {
        throw new Exception('Client already has an appointment scheduled');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Insert main appointment record
    $stmt = $pdo->prepare("
        INSERT INTO appointments (
            client_id, 
            appointment_type, 
            total_appointments, 
            total_appointment_weeks, 
            created_at, 
            updated_at
        ) VALUES (
            :client_id,
            'auto',
            :total_appointments,
            :total_weeks,
            NOW(),
            NOW()
        )
    ");

    $stmt->execute([
        ':client_id' => $clientId,
        ':total_appointments' => $totalAppointments,
        ':total_weeks' => $totalWeeks
    ]);

    $appointmentId = $pdo->lastInsertId();

    // Insert weekly appointment details
    $stmt = $pdo->prepare("
        INSERT INTO appointment_details (
            appointment_id,
            week_start_date,
            week_end_date,
            monday_appointment,
            tuesday_appointment,
            wednesday_appointment,
            thursday_appointment,
            friday_appointment,
            saturday_appointment,
            sunday_appointment,
            created_at,
            updated_at
        ) VALUES (
            :appointment_id,
            :week_start_date,
            :week_end_date,
            :monday,
            :tuesday,
            :wednesday,
            :thursday,
            :friday,
            :saturday,
            :sunday,
            NOW(),
            NOW()
        )
    ");

    foreach ($weeklyAppointments as $week) {
        $stmt->execute([
            ':appointment_id' => $appointmentId,
            ':week_start_date' => $week['start_date'],
            ':week_end_date' => $week['end_date'],
            ':monday' => in_array('Mon', $week['days']) ? 1 : 0,
            ':tuesday' => in_array('Tue', $week['days']) ? 1 : 0,
            ':wednesday' => in_array('Wed', $week['days']) ? 1 : 0,
            ':thursday' => in_array('Thu', $week['days']) ? 1 : 0,
            ':friday' => in_array('Fri', $week['days']) ? 1 : 0,
            ':saturday' => in_array('Sat', $week['days']) ? 1 : 0,
            ':sunday' => in_array('Sun', $week['days']) ? 1 : 0
        ]);
    }

    // Commit transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Appointment created successfully',
        'appointment_id' => $appointmentId
    ]);

} catch (Exception $e) {
    // Rollback transaction if there was an error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
