<?php
session_start();

// Set the response content type to JSON
header('Content-Type: application/json');
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include necessary files for database and medical record management
include('../database/config.php');
include('../php/medicalrecords.php');

// Create a new database connection and medical record manager
$db = new Database();
$conn = $db->getConnection();
$medicalrecords = new MedRecManager($conn);

// Get the patient type from the form input (Student, Faculty, Staff, etc.)
$patienttype = $_POST['patienttype']; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // If the form is for adding new medical records
    if (isset($_POST['addmedicalrecs'])) {
        // Get the form inputs
        $patientid = $_POST['patientid'];
        $comment = 'No Comment'; 
        $dateadded = date('Y-m-d');
        $timeadded = date('H:i:s'); 
        $files = $_FILES['uploadfile'];
        $patienttype = $_POST['patienttype']; 
        
        // Arrays to store filenames and hashed file names
        $filenames = [];
        $hashedFiles = []; 
        $duplicateFilenames = [];
    
        // Handle multiple file uploads
        for ($i = 0; $i < count($files['name']); $i++) {
            $originalName = $files['name'][$i];
            $tmpFilePath = $files['tmp_name'][$i];
    
            // Generate a hashed name for the file to avoid collisions
            $hashedName = md5(uniqid($originalName, true));
    
            // Check for duplicate files based on the original name
            if ($medicalrecords->getDuplicateFilenames($patientid, $originalName)) {
                // If a duplicate is found, add it to the list
                $duplicateFilenames[] = $originalName;
            } else {
                // If no duplicate, store the original and hashed file names
                $filenames[] = $originalName;
                $hashedFiles[] = $hashedName;
            } 
        }
    
        // If there are duplicate files, display an error message and redirect
        if (!empty($duplicateFilenames)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Duplicate files found: ' . implode(', ', $duplicateFilenames);
    
            // Redirect based on patient type
            $redirectUrl = getRedirectUrl($patienttype);
            header("Location: $redirectUrl");
            exit();
        }
    
        // Insert the medical records into the database
        $response = $medicalrecords->insertMedicalRecordbyPatient($patientid, $filenames, $hashedFiles, $comment, $dateadded, $timeadded);
    
        if ($response['status'] === 'success') {
            // Upload each file to the server if insertion was successful
            for ($i = 0; $i < count($hashedFiles); $i++) {
                $originalTmpPath = $files['tmp_name'][$i];
                $hashedName = $hashedFiles[$i];
    
                // Define the absolute path for the uploaded file
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/php-admin/uploadmedrecs/' . $hashedName;
    
                // Move the uploaded file to the destination directory
                if (!move_uploaded_file($originalTmpPath, $destination)) {
                    // If file upload fails, set error message and redirect
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload file: ' . $filenames[$i];
                    $redirectUrl = getRedirectUrl($patienttype);
                    header("Location: $redirectUrl");
                    exit();
                }
            }
    
            // Success message after all files are uploaded
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Medical record inserted and files uploaded successfully.';
    
            // Redirect based on patient type
            $redirectUrl = getRedirectUrl($patienttype);
            header("Location: $redirectUrl");
            exit();
        } else {
            // If inserting records fails, show an error
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $response['message'];
    
            // Redirect based on patient type
            $redirectUrl = getRedirectUrl($patienttype);
            header("Location: $redirectUrl");
            exit();
        }
    }

    // If the form is for editing an existing medical record
    if (isset($_POST['editmedrecs'])) {
        // Get the form inputs for editing a medical record
        $patientid = $_POST['patientid'];
        $id = $_POST['editid'];        
        $filename = $_POST['editfilename'];  
        $comment = $_POST['editcomment'];
        $patienttype = $_POST['patienttype']; 

        // Update the medical record in the database
        $response = $medicalrecords->updateMedicalRecordbyPatient($id, $patientid, $filename, $comment);

        if ($response['status'] === 'success') {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = $response['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $response['message'];
        }

        // Redirect based on patient type
        $redirectUrl = getRedirectUrl($patienttype);
        header("Location: $redirectUrl");
        exit();
    }

} else {
    // If the request method is not POST, show an error message
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';

    // Redirect based on patient type
    $redirectUrl = getRedirectUrl($patienttype);
    header("Location: $redirectUrl");
    exit();
}

// Helper function to determine redirect URL based on patient type
function getRedirectUrl($patienttype) {
    switch ($patienttype) {
        case 'Student':
            return 'patstudent.php';
        case 'Faculty':
            return 'patfaculty.php';
        case 'Staff':
            return 'patstaff.php';
        case 'Extension':
            return 'patextension.php';
        default:
            return 'patient-record.php'; 
    }
}
?>