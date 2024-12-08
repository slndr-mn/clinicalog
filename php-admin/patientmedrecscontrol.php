<?php
session_start();

// Include database configuration and the medical records management class
include('../database/config.php');
include('../php/medicalrecords.php');

// Create instances of the database connection and medical records manager
$db = new Database();
$conn = $db->getConnection();
$medicalrecords = new MedRecManager($conn);

// Get patient type from POST request
$patienttype = $_POST['patienttype']; 

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check if the 'addmedicalrecs' form was submitted
    if (isset($_POST['addmedicalrecs'])) {
        
        // Get form data from POST request
        $patientid = $_POST['patientid'];
        $medrecid = $_POST['medrecid'];
        $comment = 'No Comment'; 
        $dateadded = date('Y-m-d'); 
        $timeadded = date('H:i:s'); 
        $files = $_FILES['uploadfile'];
        $patienttype = $_POST['patienttype']; 
        $admin_id = $_POST['admin_id']; 
        
        $filenames = []; 
        $hashedFiles = []; 
        $duplicateFilenames = []; 

        // Loop through each uploaded file to process it
        for ($i = 0; $i < count($files['name']); $i++) {
            $originalName = $files['name'][$i];
            $tmpFilePath = $files['tmp_name'][$i]; 

            // Create a unique hash for the file to prevent name conflicts
            $hashedName = md5(uniqid($originalName, true));

            // Check if the file is already uploaded for this patient
            if ($medicalrecords->getDuplicateFilenames($patientid, $originalName)) {
                // If duplicate, add to duplicate filenames list
                $duplicateFilenames[] = $originalName;
            } else {
                // If not duplicate, add to filenames and hashed files lists
                $filenames[] = $originalName;
                $hashedFiles[] = $hashedName;
            } 
        }

        // If there are any duplicate files, show an error and redirect
        if (!empty($duplicateFilenames)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Duplicate files found: ' . implode(', ', $duplicateFilenames);

            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit(); 
        }

        // Insert medical record into database
        $response = $medicalrecords->insertMedicalRecord($admin_id, $patientid, $filenames, $hashedFiles, $comment, $dateadded, $timeadded);
    
        // Check if the insert was successful
        if ($response['status'] === 'success') {
            // Loop through the hashed files and move them from temporary folder to the upload folder
            for ($i = 0; $i < count($hashedFiles); $i++) {
                $originalTmpPath = $files['tmp_name'][$i]; 
                $hashedName = $hashedFiles[$i]; 
                $destination = 'uploadmedrecs/' . $hashedName; 

                // Move the file to the destination
                if (move_uploaded_file($originalTmpPath, $destination)) {
                    // File upload success, no action needed here
                } else {
                    // File upload failed, set session error and redirect
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload file: ' . $filenames[$i];
                    
                    // Redirect based on patient type
                    switch ($patienttype) {
                        case 'Student':
                            $redirectUrl = 'patient-studprofile.php';
                            break;
                        case 'Faculty':
                            $redirectUrl = 'patient-facultyprofile.php';
                            break;
                        case 'Staff':
                            $redirectUrl = 'patient-staffprofile.php';
                            break;
                        case 'Extension':
                            $redirectUrl = 'patient-extensionprofile.php';
                            break;
                        default:
                            $redirectUrl = 'patient-record.php';
                            break;
                    }
                    header("Location: $redirectUrl");
                    exit();
                }
            }

            // Success message after successful insertion and file upload
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Medical record inserted and files uploaded successfully.';

            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit();
        } else {
            // Error during insert, show error and redirect
            $_SESSION['status'] = 'error'; 
            $_SESSION['message'] = $response['message'];
            
            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit();
        }
    }

    // Check if the 'editmedrecs' form was submitted
    if (isset($_POST['editmedrecs'])) {
        // Get form data for editing
        $patientid = $_POST['patientid'];
        $id = $_POST['editid'];        
        $filename = $_POST['editfilename'];  
        $comment = $_POST['editcomment'];
        $patienttype = $_POST['patienttype']; 
        $admin_id = $_POST['admin_id'];

        // Call the function to update the medical record
        $response = $medicalrecords->updateMedicalRecord($admin_id, $id, $patientid, $filename, $comment);

        // Check the response and set session message accordingly
        if ($response['status'] === 'success') {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = $response['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $response['message'];
        }

        // Redirect based on patient type
        switch ($patienttype) {
            case 'Student':
                $redirectUrl = 'patient-studprofile.php';
                break;
            case 'Faculty':
                $redirectUrl = 'patient-facultyprofile.php';
                break;
            case 'Staff':
                $redirectUrl = 'patient-staffprofile.php';
                break;
            case 'Extension':
                $redirectUrl = 'patient-extensionprofile.php';
                break;
            default:
                $redirectUrl = 'patient-record.php';
                break;
        }
        header("Location: $redirectUrl");
        exit();
    }

    // Check if a request to delete a file from a medical record is made
    if (isset($_POST['medrec_id'], $_POST['file_name'], $_POST['admin_id'])) {
        try {
            // Enable error reporting
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            header('Content-Type: application/json');

            // Get data from the request
            $medrecId = $_POST['medrec_id'];
            $fileName = $_POST['file_name'];
            $adminId = $_POST['admin_id'];

            // Fetch file path from database
            $filePath = $medicalrecords->getFilePathByMedicalRecId($medrecId);
            if ($filePath && $filePath == $fileName) {
                // If the file exists, attempt to delete it from the server
                $fullPath = "uploadmedrecs/" . $filePath;
                if (file_exists($fullPath)) {
                    if (!unlink($fullPath)) {
                        echo json_encode(['success' => false, 'message' => 'Failed to delete the file.']);
                        exit();
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'File does not exist.']);
                    exit();
                }
            }

            // Delete the record from the database
            $deleteResult = $medicalrecords->deleteMedicalRecord($adminId, $medrecId);
            echo json_encode($deleteResult);

        } catch (Exception $e) {
            // Handle exception and output error message
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        // If invalid input, return error
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }

} else {
    // If the request method is not POST, show an error
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';

    // Redirect based on patient type
    switch ($patienttype) {
        case 'Student':
            $redirectUrl = 'patient-studprofile.php';
            break;
        case 'Faculty':
            $redirectUrl = 'patient-facultyprofile.php';
            break;
        case 'Staff':
            $redirectUrl = 'patient-staffprofile.php';
            break;
        case 'Extension':
            $redirectUrl = 'patient-extensionprofile.php';
            break;
        default:
            $redirectUrl = 'patient-record.php';
            break;
    }
    header("Location: $redirectUrl");
    exit();
}
?>