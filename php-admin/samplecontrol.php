<?php
session_start(); 

// Include necessary files containing configuration and class definitions
include('../database/config.php');
include('../php/patient.php'); 

// Create an instance of the Database class and establish a connection
$db = new Database();
$conn = $db->getConnection(); 

// Create an instance of the PatientManager class for handling patient-related operations
$patientManager = new PatientManager($conn);

// Process form submission when the request method is POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Collect form data for personal information
    $lname = $_POST['lname']; 
    $fname = $_POST['fname'];
    $mname = $_POST['mname']; 
    $dob = $_POST['dob']; 
    $email = $_POST['email']; 
    $connum = $_POST['connum']; 
    $sex = $_POST['sex']; 

    // Handle profile picture upload (if any file is uploaded)
    $profile = ''; // Default profile value if no file is uploaded
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        // Get the temporary file path and original file name
        $profile_tmp = $_FILES['profile']['tmp_name'];
        $profile_original_name = $_FILES['profile']['name'];

        // Generate a unique file name using md5 hash
        $profile_hash = md5(uniqid($profile_original_name, true));
        $profile_ext = pathinfo($profile_original_name, PATHINFO_EXTENSION); 
        $profile_name = $profile_hash . '.' . strtolower($profile_ext);
        $upload_dir = 'uploads/'; 
        $profile = $upload_dir . $profile_name; 

        // Move the uploaded file to the designated directory
        move_uploaded_file($profile_tmp, $profile);
    }

    // Collect form data for faculty-specific details
    $idnum = $_POST['idnum']; 
    $college = $_POST['college']; 
    $depart = $_POST['depart']; 
    $role = $_POST['role']; 

    // Collect form data for address information
    $region = $_POST['region'];
    $province = $_POST['province']; 
    $municipality = $_POST['municipality']; 
    $barangay = $_POST['barangay']; 
    $prkstrtadd = $_POST['prkstrtadd']; 

    // Collect form data for emergency contact information
    $conname = $_POST['conname']; 
    $relationship = $_POST['relationship']; 
    $emergency_connum = $_POST['emergency_connum']; 

    // Additional details for the new faculty patient
    $type = 'faculty'; 
    $dateadded = date('Y-m-d'); 
    $password = password_hash($idnum, PASSWORD_DEFAULT); 
    $status = 'active'; 
    $code = rand(100000, 999999); 
    // Call the method addFacultyPatient from the PatientManager class to insert the faculty patient data into the database
    $response = $patientManager->addFacultyPatient(
        $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $college, $depart, $role, 
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    );

    // Handle the response from the addFacultyPatient method
    if ($response['status'] === 'success') {
        // If the patient was successfully added, show a success message
        echo "Faculty patient added successfully!";
    } else {
        // If there was an error, display the error message
        echo "Error: " . $response['message'];
    }
}
?>
