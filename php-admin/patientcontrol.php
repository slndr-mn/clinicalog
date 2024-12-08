<?php
session_start();

// Include database configuration and the PatientManager class
include('../database/config.php');
include('../php/patient.php');

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();
$patient = new PatientManager($conn);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addstudentpatient'])) {

        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['studentID']; 
        $program = $_POST['program'];
        $major = $_POST['major'];
        $year = $_POST['year'];
        $section = $_POST['section']; 
        $region = $_POST['region'];
        $province = $_POST['province']; 
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $admin_id = $_POST['admin_id'];


        $profile = ''; // Default to empty if no profile picture uploaded

        // Handle Profile Upload
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile'];
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                // Move uploaded file
                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $profile = $profile_name; // Set profile name to save to the database
                } else {
                    $_SESSION['error'] = 'Failed to upload profile picture.';
                    header('Location: add-student.php'); // Redirect to the add student page
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                header('Location: add-student.php'); 
                exit();
            }
            finfo_close($finfo);
        }

        // Validate email and proceed with insertion
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Call the addStudentPatient method and store the response
            $response = $patient->addStudentPatient(
                $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, 'Student', 
                date('Y-m-d H:i:s'), password_hash($idnum, PASSWORD_DEFAULT), 'Active', 0, 
                $idnum, $program, $major, $year, $section, 
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );

            // Set session messages and status based on response
            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }

        // Redirect back to the add student page
        header('Location: add-student.php'); 
        exit();
    }
     // Add Faculty Patient logic
    if (isset($_POST['addfacultypatient'])) {

        // Initialize variables from POST data
        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['facultyID'];
        $college = $_POST['college'];
        $department = $_POST['department'];
        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province'];
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $admin_id = $_POST['admin_id'];

        $profile = ''; // Default to empty if no profile picture uploaded

        // Handle Profile Upload
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile'];
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                // Move uploaded file
                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $profile = $profile_name; // Set profile name to save to the database
                } else {
                    $_SESSION['error'] = 'Failed to upload profile picture.';
                    header('Location: add-faculty.php'); // Redirect to the add student page
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                header('Location: add-faculty.php'); 
                exit();
            }
            finfo_close($finfo);
        }

        // Validate email and proceed with insertion
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Call the addStudentPatient method and store the response
            $response = $patient->addFacultyPatient(
             $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, 'faculty', 
                date('Y-m-d'), password_hash($idnum, PASSWORD_DEFAULT), 'Active', 0, 
                $idnum, $college, $department, $role,
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );

            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }

        header('Location: add-faculty.php'); 
        exit();
    }
    // Add Staff Patient logic
    if (isset($_POST['addstaffpatient'])) {

        // Initialize variables from POST data
        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber']; 
        $sex = $_POST['sex'];
        $idnum = $_POST['staffID'];
        $office = $_POST['office'];
        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province'];
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $admin_id = $_POST['admin_id'];

        $profile = ''; // Default to empty if no profile picture uploaded

        // Handle Profile Upload
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile'];
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                // Move uploaded file
                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $profile = $profile_name; // Set profile name to save to the database
                } else {
                    $_SESSION['error'] = 'Failed to upload profile picture.';
                    header('Location: add-staff.php'); // Redirect to the add student page
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                header('Location: add-staff.php'); 
                exit();
            }
            finfo_close($finfo);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = $patient->addStaffPatient(
               $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, 'Staff', 
                date('Y-m-d H:i:s'), password_hash($idnum, PASSWORD_DEFAULT), 'Active', 0, 
                $idnum, $office, $role,
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );

            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }

        header('Location: add-staff.php'); 
        exit();
    }
    // Add Extension Patient logic
    if (isset($_POST['addextensionpatient'])) {

        // Initialize variables from POST data
        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['extentID'];
        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province'];
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $admin_id = $_POST['admin_id'];

        $profile = ''; // Default to empty if no profile picture uploaded

        // Handle Profile Upload
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile'];
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                // Move uploaded file
                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $profile = $profile_name; // Set profile name to save to the database
                } else {
                    $_SESSION['error'] = 'Failed to upload profile picture.';
                    header('Location: add-extension.php'); // Redirect to the add student page
                    exit();
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                header('Location: add-extension.php'); 
                exit();
            } 
            finfo_close($finfo);
        }

        // Validate email and proceed with insertion
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Call the addStudentPatient method and store the response
            $response = $patient->addExtenPatient(
               $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, 'Extension', 
                date('Y-m-d H:i:s'), password_hash($idnum, PASSWORD_DEFAULT), 'Active', 0, 
                $idnum, $role,
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );

            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }

        header('Location: add-extension.php'); 
        exit();
    }
    // Edit Student Patient logic
    if (isset($_POST['editstudentpatient'])) {
        // Retrieve patient and personal details from the form
        $patientid = $_POST['patientid']; 
        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['studentID'];
        $admin_id = $_POST['admin_id'];

        // Handle program and major fields with custom program/major option
        $program = (!empty($_POST['program']) &&  $_POST['program'] !== 'Click to type...') ? 
                        $_POST['program'] : $_POST['customProgram'];
        $major = (!empty($_POST['major']) &&  $_POST['major'] !== 'Click to type...' && empty($_POST['customProgram']) ) ? 
                        $_POST['major'] : $_POST['customMajor'];

        // Other personal details
        $year = $_POST['year'];
        $section = $_POST['section']; 
        $region = $_POST['region'];
        $province = $_POST['province']; 
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $status = $_POST['Status'];

        // Validate email format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $response = $patient->updateStudentPatient(
               $admin_id, $patientid, 
                $lname, $fname, $mname, $dob, $email, $connum, $sex,
                password_hash($idnum, PASSWORD_DEFAULT), $status, 
                $idnum, $program, $major, $year, $section, 
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );
    
            // Store success or error message in session
            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];

            // Check if a profile picture was uploaded
            if (!empty($_FILES['addprofile']['name']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
                $profile = $_FILES['addprofile'];
                $profile_original_name = basename($profile['name']);
                $profile_tmp = $profile['tmp_name'];

                // Validate the uploaded file type (only JPEG or PNG allowed)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $profile_tmp);
                $allowed_mimes = ['image/jpeg', 'image/png'];
    
                if (in_array($mime, $allowed_mimes)) {
                    $profile_hash = md5(uniqid($profile_original_name, true));
                    $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                    $uploadDir = 'uploads/';
                    $profile_destination = $uploadDir . $profile_name;

                    // Move the uploaded file to the designated directory
                    if (move_uploaded_file($profile_tmp, $profile_destination)) {

                        // Update the patient's profile image in the database
                        $imageResponse = $patient->updatePatientProfileImage($patientid, $profile_name);
                        
                        // Append success or error message for image upload
                        $_SESSION['message'] .= ' ' . $imageResponse['message'];
                        $_SESSION['status'] = $imageResponse['status'];
                    } else {
                        $_SESSION['error'] = 'Failed to upload profile picture.'; 
                    }
                } else {
                    $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                }
                finfo_close($finfo);
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }
    
        // Redirect back to the student edit page
        header('Location: patient-studedit.php'); 
        exit();
    }
    if (isset($_POST['editfacultypatient'])) {
        $patientid = $_POST['patientid']; 

        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['facultyID'];
        $admin_id = $_POST['admin_id'];

        $college= (!empty($_POST['college']) &&  $_POST['college'] !== 'Click to type...') ? 
                        $_POST['college'] : $_POST['customCollege'];
        $department = (!empty($_POST['department']) &&  $_POST['department'] !== 'Click to type...' && empty($_POST['customCollege']) ) ? 
                        $_POST['department'] : $_POST['customDepartment'];

        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province']; 
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $status = $_POST['Status'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $response = $patient->updateFacultyPatient(
               $admin_id, $patientid, 
                $lname, $fname, $mname, $dob, $email, $connum, $sex,
                password_hash($idnum, PASSWORD_DEFAULT), $status, 
                $idnum, $college, $department, $role, 
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );
    
            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];

            if (!empty($_FILES['addprofile']['name']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
                $profile = $_FILES['addprofile'];
                $profile_original_name = basename($profile['name']);
                $profile_tmp = $profile['tmp_name'];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $profile_tmp);
                $allowed_mimes = ['image/jpeg', 'image/png'];
    
                if (in_array($mime, $allowed_mimes)) {
                    $profile_hash = md5(uniqid($profile_original_name, true));
                    $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                    $uploadDir = 'uploads/';
                    $profile_destination = $uploadDir . $profile_name;

                    if (move_uploaded_file($profile_tmp, $profile_destination)) {

                        $imageResponse = $patient->updatePatientProfileImage($patientid, $profile_name);
                        
                        $_SESSION['message'] .= ' ' . $imageResponse['message'];
                        $_SESSION['status'] = $imageResponse['status'];
                    } else {
                        $_SESSION['error'] = 'Failed to upload profile picture.'; 
                    }
                } else {
                    $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                }
                finfo_close($finfo);
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }
    
        header('Location: patient-facultyedit.php'); 
        exit();
    }
    if (isset($_POST['editstaffpatient'])) {
        $patientid = $_POST['patientid']; 

        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['staffID'];

        $office= (!empty($_POST['office']) &&  $_POST['office'] !== 'Click to type...') ? 
                        $_POST['office'] : $_POST['customOffice'];
        
        $admin_id = $_POST['admin_id'];

        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province']; 
        $municipality = $_POST['municipality']; 
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $status = $_POST['Status'];

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $response = $patient->updateStaffPatient(
                $admin_id, $patientid, 
                $lname, $fname, $mname, $dob, $email, $connum, $sex,
                password_hash($idnum, PASSWORD_DEFAULT), $status, 
                $idnum, $office, $role, 
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );
    
            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];

            if (!empty($_FILES['addprofile']['name']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
                $profile = $_FILES['addprofile'];
                $profile_original_name = basename($profile['name']);
                $profile_tmp = $profile['tmp_name'];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $profile_tmp);
                $allowed_mimes = ['image/jpeg', 'image/png'];
    
                if (in_array($mime, $allowed_mimes)) {
                    $profile_hash = md5(uniqid($profile_original_name, true));
                    $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                    $uploadDir = 'uploads/';
                    $profile_destination = $uploadDir . $profile_name;

                    if (move_uploaded_file($profile_tmp, $profile_destination)) {

                        $imageResponse = $patient->updatePatientProfileImage($patientid, $profile_name);
                        
                        $_SESSION['message'] .= ' ' . $imageResponse['message'];
                        $_SESSION['status'] = $imageResponse['status'];
                    } else {
                        $_SESSION['error'] = 'Failed to upload profile picture.'; 
                    }
                } else {
                    $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                }
                finfo_close($finfo); 
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }
    
        header('Location: patient-staffedit.php'); 
        exit();
    }
    if (isset($_POST['editextenpatient'])) {
        $patientid = $_POST['patientid']; 

        $lname = $_POST['lastName'];
        $fname = $_POST['firstName'];
        $mname = $_POST['middleName'];
        $dob = $_POST['dob'];
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $connum = $_POST['contactNumber'];
        $sex = $_POST['sex'];
        $idnum = $_POST['extenID'];
        $role = $_POST['role'];
        $region = $_POST['region'];
        $province = $_POST['province']; 
        $municipality = $_POST['municipality'];
        $barangay = $_POST['barangay'];
        $prkstrtadd = $_POST['street'];
        $conname = $_POST['emergencyContactName'];
        $relationship = $_POST['relationship'];
        $emergency_connum = $_POST['emergencyContactNumber'];
        $status = $_POST['Status'];
        $admin_id = $_POST['admin_id'];


        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $response = $patient->updateExtenPatient(
               $admin_id, $patientid, 
                $lname, $fname, $mname, $dob, $email, $connum, $sex,
                password_hash($idnum, PASSWORD_DEFAULT), $status, 
                $idnum, $role, 
                $region, $province, $municipality, $barangay, 
                $prkstrtadd, $conname, $relationship, $emergency_connum
            );
    
            $_SESSION['message'] = $response['message'];
            $_SESSION['status'] = $response['status'];

            if (!empty($_FILES['addprofile']['name']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
                $profile = $_FILES['addprofile'];
                $profile_original_name = basename($profile['name']);
                $profile_tmp = $profile['tmp_name'];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $profile_tmp);
                $allowed_mimes = ['image/jpeg', 'image/png'];
    
                if (in_array($mime, $allowed_mimes)) {
                    $profile_hash = md5(uniqid($profile_original_name, true));
                    $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                    $uploadDir = 'uploads/';
                    $profile_destination = $uploadDir . $profile_name;

                    if (move_uploaded_file($profile_tmp, $profile_destination)) {

                        $imageResponse = $patient->updatePatientProfileImage($patientid, $profile_name);
                        
                        $_SESSION['message'] .= ' ' . $imageResponse['message'];
                        $_SESSION['status'] = $imageResponse['status'];
                    } else {
                        $_SESSION['error'] = 'Failed to upload profile picture.'; 
                    }
                } else {
                    $_SESSION['error'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
                }
                finfo_close($finfo);
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }
    
        header('Location: patient-extenedit.php'); 
        exit();
    }
    
// If none of the above conditions are met, show an error message for invalid request method
} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';

    // Redirect to the patient record page
    header('Location: patient-record.php'); 
    exit();
}
?>