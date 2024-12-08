<?php
session_start();

// Set content type to JSON and enable error reporting for debugging
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary class files and database configuration
include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');
include('../php/offcampus.php'); 
@include('../php/patient-studprofile.php');
@include('../php/patient-staffprofile.php');
@include('../php/patient-facultyprofile.php'); 
@include('../php/patient-extensionprofile.php');
include('../php/consultation.php');

// Initialize database connection and class instances
$db = new Database();
$conn = $db->getConnection();
$consultationManager = new ConsultationManager($conn); 
$medicineManager = new MedicineManager($conn); 
$offcampusManager = new OffCampusManager($conn);

// Decode incoming JSON payload (for API requests)
$data = json_decode(file_get_contents('php://input'), true);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    // Handle adding a new off-campus record
    if (isset($_POST['addoffcampus'])) {
        // Gather input data
        $adminId = $_POST['admin_id'];
        $date = date('Y-m-d'); // Current date
        $medstock_id = $_POST['selected_medicine_id'] ?? null;
        $treatment_medqty = isset($_POST['presmedqty']) ? (int)$_POST['presmedqty'] : null;

        if ($medstock_id && $treatment_medqty) {
            // Check available quantity of the selected medicine
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
            if ($treatment_medqty > $availableQty) {
                // Insufficient stock error
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            // Insert the off-campus record
            $offcampusresult = $offcampusManager->insertOffCampusRecord($adminId, $medstock_id, $treatment_medqty, $date);

            // Handle result of insertion
            if ($offcampusresult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = $offcampusresult['message'];
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $offcampusresult['message'];
            }
        } else {
            // Missing input error
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
        }

        // Redirect to the off-campus add page
        header('Location: offcampusadd.php');
        exit();
    }

    // Handle updating an existing off-campus record
    if (isset($_POST['updateoffcampus'])) {
        // Gather input data
        $adminId = $_POST['admin_id'];
        $date = $_POST['editdate'];
        $medstock_id = $_POST['editmedstockid'] ?? null;
        $treatment_medqty = isset($_POST['editmedqty']) ? (int)$_POST['editmedqty'] : null;
        $offcampus_id = $_POST['editid'] ?? null; 

        if ($medstock_id && $treatment_medqty && $offcampus_id) {
            // Check available quantity of the selected medicine
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
            if ($treatment_medqty > $availableQty) {
                // Insufficient stock error
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            // Update the off-campus record
            $updateResult = $offcampusManager->updateOffCampusRecord($adminId, $offcampus_id, $medstock_id, $treatment_medqty, $date);
            if ($updateResult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Off-campus record updated successfully.';
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $updateResult['message'];
            }
        } else {
            // Missing input error
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID, quantity, or record ID.";
        }

        // Redirect to the off-campus add page
        header('Location: offcampusadd.php');
        exit();
    }

    // Handle deleting an off-campus record
    if (isset($_POST['deleteoffcampus'])) {
        // Set content type for JSON response
        header('Content-Type: application/json'); 
        $adminId = $_POST['admin_id'];
        $offcampus_id = $_POST['offcampus_id'] ?? null;

        if ($offcampus_id) {
            // Delete the off-campus record
            $deleteResult = $offcampusManager->deleteOffCampusRecord($adminId, $offcampus_id);
            if ($deleteResult['status'] === 'success') {
                // Respond with success
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
            } else {
                // Respond with failure
                echo json_encode(['status' => 'error', 'message' => $deleteResult['message']]);
            }
        } else {
            // Missing record ID error
            echo json_encode(['status' => 'error', 'message' => 'Missing Record ID']);
        }
        exit();
    }

} else {
    // Handle invalid request method
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: offcampusadd.php');
    exit();
}
?>
