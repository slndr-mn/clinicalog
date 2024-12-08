<?php
session_start(); 

// Check if the user is logged in, if not, redirect to the login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit; 
}

// Include necessary files for database connection and user management
include('../database/config.php');
include('../php/user.php');

// Create an instance of the Database class to establish a connection to the database
$db = new Database();
$conn = $db->getConnection(); 

// Create an instance of the User class to handle user-related operations
$user = new User($conn);

// Initialize a variable to store the hashed password (only needed for new users)
$password = '';

// Handle POST requests to manage staff users (add, update, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // If the 'addstaff' form is submitted, handle adding a new staff user
    if (isset($_POST['addstaff'])) {
        
        // Collect input data from the form fields
        $id = trim($_POST['addid']);
        $first_name = $_POST['addfname']; 
        $last_name = $_POST['addlname'];
        $middle_name = $_POST['addmname'];
        $email = filter_var($_POST['addemail'], FILTER_SANITIZE_EMAIL);
        $position = $_POST['addposition'];
        $role = $_POST['addrole'];
        $status = $_POST['addstatus'];
        $dateadded = date('Y-m-d H:i:s');  
        $password = password_hash($id, PASSWORD_BCRYPT); 
        $admin_id = $_POST['admin_id'];  
        $code = 0; 

        // Handle profile picture upload (if any)
        $user_profile = '';
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile']; 
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            // Check if the uploaded file is a valid image
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            // If the file is a valid image, proceed with saving it
            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));  
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                // Move the uploaded file to the 'uploads' directory
                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $user_profile = $profile_name; 
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload profile picture.';
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Invalid file type.'; 
            }
            finfo_close($finfo);
        }

        // Validate email format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // If email is valid, call the User class method to register the new user
            if ($user->register($id, $first_name, $last_name, $middle_name, $email, $position, $role, $status, $dateadded, $user_profile, $password, $code, $admin_id)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User registered successfully!';
                header('Location: staffuser.php');
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Registration failed. Please try again.';
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }

        // Redirect to the staff user page
        header('Location: staffuser.php');
        exit();
    }

    // If the 'updateuser' form is submitted, handle updating an existing user
    if (isset($_POST['updateuser'])) {

        // Collect the new values to update the user details
        $user_oldid = $_POST['editoldid'];
        $user_idnum = $_POST['editid'];  
        $new_fname = $_POST['editfname'];
        $new_lname = $_POST['editlname'];
        $new_mname = $_POST['editmname'];
        $new_email = filter_var($_POST['editemail'], FILTER_SANITIZE_EMAIL);
        $new_position = $_POST['editposition'];
        $new_role = $_POST['editrole'];
        $new_status = $_POST['editstatus'];
        $admin_id = $_POST['admin_id']; 

        // Handle profile picture update (if any)
        $new_profile = null;
        if (isset($_FILES['editprofile']) && $_FILES['editprofile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['editprofile']['tmp_name'];

            // Check if the uploaded file is a valid image
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $fileTmpPath);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            // If the file is valid, process and save it
            if (in_array($mime, $allowed_mimes)) {
                $fileHash = md5(uniqid($_FILES['editprofile']['name'], true));  
                $new_profile = $fileHash . '.' . strtolower(pathinfo($_FILES['editprofile']['name'], PATHINFO_EXTENSION));
                $uploadFileDir = 'uploads/';
                $dest_path = $uploadFileDir . $new_profile;

                // Move the file to the destination folder
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Error moving the uploaded file.';
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Invalid file type.';  
            }
            finfo_close($finfo);
        }

        // Update user details in the database
        if ($user->updateUser($admin_id, $user_oldid, $user_idnum, $new_fname, $new_lname, $new_mname, $new_email, $new_position, $new_role, $new_status)) {

            // If the profile picture was updated, update it as well
            if ($new_profile) {
                if ($user->updateProfilePicture($user_idnum, $new_profile)) {
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = 'User updated successfully!';
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'User updated, but failed to update profile picture.';
                }
            } else {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User updated successfully!';
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Failed to update user.';  // Failed to update user
        }

        // Redirect to the staff user page
        header('Location: staffuser.php');
        exit();
    }

    // If a user ID is provided, handle user deletion
    if (isset($_POST['user_idnum'])) {
        $user_idnum = $_POST['user_idnum'];
        
        // Attempt to delete the user and return success or failure response
        if ($user->deleteUser($user_idnum)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $_SESSION['message']]);
        }
    } else {
        // If no user ID is provided, return an error message
        echo json_encode(['success' => false, 'message' => 'No user ID provided']);
    }
}
?>