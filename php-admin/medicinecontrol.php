<?php
session_start();

// Include the necessary configuration and class files
include('../database/config.php'); 
include('../php/medicine.php'); 

// Initialize database connection and MedicineManager instance
$db = new Database();
$conn = $db->getConnection();
$medicine = new MedicineManager($conn);

// Check the request method to ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    // Adding a new medicine to the inventory
    if (isset($_POST['addMedicine'])) {
        // Gather input data
        $medicine_id = $_POST['addname'];
        $medicine_qty = $_POST['addquantity'];
        $medicine_dosage = $_POST['addDS'];
        $medicine_dateadded = date('Y-m-d');
        $medicine_timeadded = date('h:i:s'); 
        $medicine_expirationdt = $_POST['addED'];
        $medicine_disable = "0"; 
        $admin_id = $_POST['admin_id'];

        // Call the insertMedstock method to add medicine to the database
        if ($medicine->insertMedstock($admin_id, $medicine_id, $medicine_qty, $medicine_dosage, $medicine_dateadded, $medicine_timeadded, $medicine_expirationdt, $medicine_disable)) {
            // Success response
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = "Medicine added successfully";
        } else {
            // Failure response
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Failed to add medicine";
        }

        // Redirect to the medicine table page
        header('Location: medicinetable.php');
        exit();
    }

    // Updating an existing medicine in the inventory
    if (isset($_POST['updatemedicine'])) {
        // Gather input data
        $medstock_id = $_POST['editid'];
        $medicine_name = $_POST['editname'];
        $medicine_qty = $_POST['editquantity'];
        $medicine_dosage = $_POST['editDS'];
        $medicine_expirationdt = $_POST['editED'];
        $medicine_disable = $_POST['editDisable'];
        $admin_id = $_POST['admin_id'];

        // Call the updateMedstock method to update the medicine details
        $result = $medicine->updateMedstock($admin_id, $medstock_id, $medicine_name, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable);

        // Handle the result of the update operation
        if ($result['status'] === 'success') {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = $result['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $result['message'];
        }

        // Redirect to the medicine table page
        header('Location: medicinetable.php');
        exit();
    }

    // Adding or updating a medicine entry
    if (isset($_POST['addmed'])) {
        // Gather input data
        $medicine_id = $_POST['medicineId'];
        $medicine_name = $_POST['medicineName'];
        $medicine_category = $_POST['medicineCategory'];
        $admin_id = $_POST['admin_id'];

        // Handle new medicine creation
        if (empty($medicine_id)) {
            // Check if a medicine with the same name already exists
            if ($medicine->medicines->medicineExists($medicine_name)) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Medicine with this name already exists.";
            } else {
                // Insert the new medicine
                if ($medicine->insertMedicine($admin_id, $medicine_name, $medicine_category)) {
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = "Medicine added successfully";
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = "Failed to add medicine";
                }
            }
        } else {
            // Handle existing medicine update
            $existingMedicine = $medicine->medicines->find($medicine_id);
            if ($existingMedicine) {
                // Check if the name has changed and validate uniqueness
                if ($existingMedicine->medicine_name !== $medicine_name) {
                    if ($medicine->medicines->medicineExists($medicine_name)) {
                        $_SESSION['status'] = 'error';
                        $_SESSION['message'] = "Medicine with this name already exists.";
                    } else {
                        // Update the medicine details
                        if ($medicine->updateMedicine($admin_id, $medicine_id, $medicine_name, $medicine_category)) {
                            $_SESSION['status'] = 'success';
                            $_SESSION['message'] = "Medicine updated successfully";
                        } else {
                            $_SESSION['status'] = 'error';
                            $_SESSION['message'] = "Failed to update medicine";
                        }
                    }
                } else {
                    // Update the medicine details without changing the name
                    if ($medicine->updateMedicine($admin_id, $medicine_id, $medicine_name, $medicine_category)) {
                        $_SESSION['status'] = 'success';
                        $_SESSION['message'] = "Medicine updated successfully";
                    } else {
                        $_SESSION['status'] = 'error';
                        $_SESSION['message'] = "Failed to update medicine";
                    }
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Medicine not found.";
            }
        }

        // Redirect to the medicine table page
        header('Location: medicinetable.php');
        exit();
    }

    // Deleting a medicine entry
    if (isset($_POST['medicine_id'])) {
        $medicine_id = $_POST['medicine_id'];

        // Call the deleteMedicine method to remove the medicine
        if ($medicine->deleteMedicine($medicine_id)) {
            echo json_encode(['success' => true]); 
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete medicine']); // Respond with failure
        }
        exit();
    }
}
?>