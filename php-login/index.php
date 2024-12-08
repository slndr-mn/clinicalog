<?php
// Disable caching to ensure the page always loads fresh data (useful for login forms)
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

// Include necessary files for database connection, user management, and patient management
include '../database/config.php';  
include '../php/user.php';         
include '../php/patient.php';      

// Instantiate Database, User, and PatientManager objects
$database = new Database();
$db = $database->getConnection();  
$user = new User($db);             
$patient = new PatientManager($db);  

// Check if the form is submitted with the email and password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {

    // Get the input values from the form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $defaultadmin = "Administrator"; 
    $doctor = "Campus Physician";   

    // Check if the user exists in the system with the provided email and password
    $userData = $user->userExists($email, $password);

    // If user data is found
    if ($userData) {
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        // Set session variables to track user login status and role
        $_SESSION['logged_in'] = true;
        $_SESSION['user_idnum'] = $userData->user_idnum;
        $_SESSION['user_status'] = $userData->user_status;
        $_SESSION['user_position'] = $userData->user_position;
        $_SESSION['user_role'] = $userData->user_role;

        // Check if the user account is active
        if ($userData->user_status === 'Active') {
            // Redirect based on the user's position and role
            if ($_SESSION['user_position'] === $defaultadmin || $_SESSION['user_position'] === $doctor) {
                header('Location: ../php-admin/index.php');  
                exit;
            } elseif ($_SESSION['user_role'] === 'Super Admin') {
                header('Location: ../php-admin/superadindex.php');  
                exit;
            } elseif ($_SESSION['user_role'] === 'Admin') {
                header('Location: ../php-admin/adminindex.php');  
                exit;
            }
        } else {
            // If the account is not active, show an error message and stay on the login page
            $_SESSION['error_message'] = "Account can't be used.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        // If user data is not found, check if the email and password belong to a patient
        $patientData = $patient->userpatientExists($email, $password);

        // If patient data is found
        if ($patientData) {
            // Check if the patient's account is active
            if ($patientData->patient_status === 'Active') {
                session_regenerate_id(true); 
                $_SESSION['logged_in'] = true;  
                $_SESSION['patuser_id'] = $patientData->patient_id;  
                $_SESSION['patuser_status'] = $patientData->patient_status;  
                $type = $_SESSION['patuser_type'] = $patientData->patient_patienttype;  

                // Redirect based on the patient's type (Student, Faculty, Staff, Extension)
                switch ($type) {
                    case 'Student': 
                        header('Location: ../php-client/patstudents.php'); 
                        break;
                    case 'Faculty':
                        header('Location: ../php-client/patfaculty.php'); 
                        break;
                    case 'Staff':
                        header('Location: ../php-client/patstaff.php'); 
                        break;
                    case 'Extension':
                        header('Location: ../php-client/patextension.php'); 
                        break;
                }
                echo json_encode(['status' => 'success']); 
                exit;
            } else {
                // If the patient's account is not active, show an error message
                $_SESSION['error_message'] = "Account can't be used.";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        } else {
            // If no valid user or patient data is found, show an error message
            $_SESSION['error_message'] = "Invalid email or password.";
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    }
}

// Check if there's any error message in the session and display it
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="stylesheet" type="text/css" href="../css/style.css">  <!-- Link to the CSS for the page -->
</head>
<body>
    <!-- Link to home page or admin dashboard (icon will be clickable, but no content in the link) -->
    <a href="../php-admin/index.php"></a>
    
    <!-- Logo and system title -->
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <!-- Login form wrapper -->
    <div class="wrapper">
        <div class="login-wrapper">
            <form id="login-form" action="" method="post" autocomplete="off">  <!-- Form for login -->
                <p id="welcome">Welcome Admin!</p>
                <p id="login2">Login to Continue</p>

                <!-- Display error message if set in the session -->
                <?php if ($error_message): ?> 
                    <p id="error-message" style="color: red; text-align: center;">
                        <?= $error_message; ?>
                    </p>
                <?php endif; ?>

                <!-- Input fields for email and password -->
                <div class="form-container">
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="text" name="email" id="email" class="form-input" placeholder="Enter your Email" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <img src="../assets/img/password.png" alt="password icon">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Enter your Password" required>
                        <input type="checkbox" id="show-password">  <!-- Checkbox to toggle password visibility -->
                    </div>

                    <!-- Link for users who forgot their password -->
                    <div class="forgotpassword"> 
                        <span id="forgot">Forgot Password?</span>
                        <span id="click"><a href="forgotpassword.php">Click Here.</a></span>
                    </div>
                </div>

                <!-- Submit button for the login form -->
                <button id="loginbtn" type="submit">Login</button>
            </form>
        </div>
    </div>
    
    <!-- Script to toggle password visibility when checkbox is checked -->
    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            if (this.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';  
            }
        });
    </script>
</body>
</html>