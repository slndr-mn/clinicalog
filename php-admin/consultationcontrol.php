<?php
session_start();

// Set the content type to JSON for API responses and enable error reporting for debugging
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files for database and business logic
include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');
@include('../php/patient-studprofile.php'); 
@include('../php/patient-staffprofile.php');
@include('../php/patient-facultyprofile.php'); 
@include('../php/patient-extensionprofile.php');
include('../php/consultation.php');

// Initialize the database connection and required managers
$db = new Database();
$conn = $db->getConnection();
$consultationManager = new ConsultationManager($conn); 
$medicineManager = new MedicineManager($conn); 

// Parse JSON input data for API calls
$data = json_decode(file_get_contents('php://input'), true);

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a new consultation
    if (isset($_POST['addcon'])) {
        // Retrieve and validate input data
        $patient_idnum = $_POST['selected_patient_id'] ?? null;
        $diagnosis = htmlspecialchars($_POST['Diagnosis'], ENT_QUOTES);
        $treatment_notes = htmlspecialchars($_POST['presmednotes'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['Remarks'], ENT_QUOTES);
        $consult_date = date('Y-m-d'); // Current date for consultation
        $adminId = $_POST['admin_id'];

        // Ensure required fields are provided
        if (!$patient_idnum || !$diagnosis) {
            $_SESSION['status'] = 'error'; 
            $_SESSION['message'] = "Missing required fields.";
            header('Location: addconsultation.php');
            exit();
        }

        // Validate medicine ID and quantity
        $medstock_id = $_POST['selected_medicine_id'] ?? null;
        $treatment_medqty = isset($_POST['presmedqty']) ? (int)$_POST['presmedqty'] : null;

        if ($medstock_id && $treatment_medqty) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);

            // Check stock availability
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: addconsultation.php');
                exit();
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
            header('Location: addconsultation.php');
            exit();
        }

        // Insert the consultation into the database
        $consultationResult = $consultationManager->insertConsultation($adminId, $patient_idnum, $diagnosis, $treatment_notes, $remark, $consult_date);

        if ($consultationResult['status'] === 'success') {
            $consult_id = $consultationResult['consult_id'];

            // Insert prescribed medicine for the consultation
            if ($consultationManager->insertPrescribemed($consult_id, $medstock_id, $treatment_medqty)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = "Consultation and prescribed medicine added successfully.";
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Consultation added, but failed to add prescribed medicine.";
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $consultationResult['message'];
        }

        header('Location: addconsultation.php'); // Redirect back to the consultation page
        exit();
    }

    // Handle editing an existing consultation
    if (isset($_POST['editcon'])) {
        // Retrieve and validate input data for editing
        $consult_id = $_POST['edit_consult_id'] ?? null;
        $medstock_id = $_POST['edit_medicine_id'] ?? null;
        $edited_medqty = isset($_POST['edit_quantity']) ? (int)$_POST['edit_quantity'] : null;
        $patient_idnum = $_POST['edit_patient_id'] ?? null;
        $diagnosis = htmlspecialchars($_POST['edit_diagnosis'], ENT_QUOTES);
        $treatment_notes = htmlspecialchars($_POST['edit_notes'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['edit_remarks'], ENT_QUOTES);
        $consult_date = $_POST['edit_date'] ?? date('Y-m-d');
        $adminId = $_POST['admin_id'];

        // Ensure required fields are provided
        if (!$consult_id || !$medstock_id || !$edited_medqty || !$patient_idnum || !$diagnosis) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing required fields.";
            header('Location: addconsultation.php');
            exit();
        }

        // Validate and check stock availability
        $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
        if ($edited_medqty > $availableQty) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
            header('Location: addconsultation.php');
            exit();
        }

        header('Location: addconsultation.php'); // Redirect to the consultation page
        exit();
    }

    // Handle deleting a consultation
    if (isset($_POST['delete'])) {
        $consult_id = $_POST['edit_consult_id'] ?? null;

        if ($consult_id) {
            $deleteResult = $consultationManager->deleteConsultation($consult_id);

            // Send JSON response
            echo json_encode([
                'status' => $deleteResult['status'],
                'message' => $deleteResult['message']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Consultation ID is missing.'
            ]);
        }

        header('Location: addconsultation.php');
        exit();
    }

    // Handle medicine stock validation via AJAX
    if (isset($_POST['medstock_id'], $_POST['requested_qty'])) {
        $medstock_id = $_POST['medstock_id'];
        $requested_qty = (int) $_POST['requested_qty'];

        exit();
    }
} else {
    // Handle invalid request method
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: addconsultation.php');
    exit();
}
?>