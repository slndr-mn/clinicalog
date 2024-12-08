<?php
session_start(); 
header('Content-Type: application/json'); 

// Include necessary files for database configuration and transaction management
include('../database/config.php');
include('../php/transaction.php');

// Create instances of the Database class to establish a connection
$db = new Database();
$conn = $db->getConnection();

// Create an instance of the TransacManager class to manage transaction logic
$transac = new TransacManager($conn);

// Handle POST requests for adding or editing transactions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Process 'addtransac' form submission to add a new transaction
    if (isset($_POST['addtransac'])) {
        $patientId = $_POST['selected_patient_id']; 
        $purpose = $_POST['transac_purpose']; 
        $adminId = $_POST['admin_id']; 

        // Call the method to add the transaction to the database
        $transaction = $transac->addTransaction($adminId, $patientId, $purpose);

        // Check if the transaction was successfully added and store the message in session
        if ($transaction['status'] == 'success') {    
            $_SESSION['message'] = $transaction['message'];  
            $_SESSION['status'] = $transaction['status'];  
        } else {
            $_SESSION['message'] = $transaction['message']; 
            $_SESSION['status'] = 'error';  
        }
        // Redirect to the transactions page after processing
        header('Location: transactions.php'); 
        exit();  

    }

    // Process 'edittransac' form submission to edit an existing transaction
    if (isset($_POST['edittransac'])) {
        $transacid = $_POST['transac_id'];  
        $patient_id = $_POST['edit_patient_id'];  
        $purpose = $_POST['edit_purpose'];  
        $adminId = $_POST['admin_id'];  

        // Call the method to update the transaction in the database
        $transaction = $transac->updatePatientAndPurpose($adminId, $transacid, $patient_id, $purpose);

        // Check if the transaction was successfully updated and store the message in session
        if ($transaction['status'] == 'success') {    
            $_SESSION['message'] = $transaction['message'];  
            $_SESSION['status'] = $transaction['status'];  
        } else {
            $_SESSION['message'] = $transaction['message'];  
            $_SESSION['status'] = 'error';  
        }
        // Redirect to the transactions page after processing
        header('Location: transactions.php'); 
        exit();  // Stop further code execution after redirect

    }

    // Handle status change for a transaction (Pending, In Progress, Done)
    if (isset($_POST['transac_id']) && isset($_POST['status']) && isset($_POST['admin_id'])) {
        $adminId = $_POST['admin_id']; 
        $transac_id = $_POST['transac_id'];  
        $status = $_POST['status'];  

        $response = [];

        // Update the status of the transaction based on the provided status
        switch ($status) {
            case 'Pending':
                $response = $transac->updateStatusToPending($adminId, $transac_id); 
                break;
            case 'Progress':
                $response = $transac->updateStatusToInProgress($adminId, $transac_id); 
                break;
            case 'Done':
                $response = $transac->updateStatusToDone($adminId, $transac_id);  
                break;
            default:
                // If the status is invalid, return an error message
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit;  
        }

        // Return the response as JSON
        echo json_encode($response);
    } else {
        // If any required parameters are missing, return an error message
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;  
    }

}
?>