<?php
session_start();

// Include necessary files for database connection, user management, and OTP handling
include '../database/config.php'; 
include '../vendor/autoload.php'; 
include '../php/user.php';   
include '../php/sentOTP.php';   

// Unset any previous session messages for error or success feedback
unset($_SESSION['message']);
unset($_SESSION['message_type']);

// Create a new Database instance and establish a connection
$database = new Database();
$db = $database->getConnection();

// Instantiate the User class to interact with user data
$user = new User($db);

// Initialize a variable for JavaScript code to be injected later
$jsScript = '';

// Check if the form was submitted with the email address
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["emaill"])) {
    // Trim and store the email from the form input
    $email = trim($_POST["emaill"]);

    // Validate the email format
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Generate a random 6-digit OTP code
        $otp = random_int(100000, 999999);  

        // Check if the email exists in the database and is valid
        if ($user->emailverify($email)) {
            // Attempt to update the OTP in the database for the user
            if ($user->updateCode($email, $otp)) {
                // Create an instance of the sentOTP class to handle OTP email sending
                $emailSender = new sentOTP();
                // Attempt to send the OTP via email
                $emailResult = $emailSender->sendOtp($email, $otp);

                // Check if the email sending was successful
                if ($emailResult['success']) {
                    // Store the email in the session for later use
                    $_SESSION['emaill'] = $email; 
                    // Set success message for JavaScript execution
                    $type = "success";
                    $jsScript = "
                        document.body.classList.add('active');
                        Swal.fire({
                            title: 'OTP Sent!',
                            text: 'Code has been sent to your email.',
                            icon: 'success',
                            confirmButtonText: 'Continue',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'verify.php';  // Redirect to the verification page
                            }
                        });
                    ";
                } else {
                    // Set error message in the session if OTP sending failed
                    $_SESSION['message'] = "An error occurred while sending the OTP.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                // Set error message if updating OTP in the database failed
                $_SESSION['message'] = "Error updating OTP.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            // Set error message if the email is not found in the database
            $_SESSION['message'] = "Wrong email input. Please try again.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Set error message if the email address is not valid
        $_SESSION['message'] = "Invalid email address provided.";
        $_SESSION['message_type'] = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  <!-- Set character encoding for the page -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  <!-- Set viewport for responsiveness -->
    <title>CIS</title>  <!-- Title of the page -->
    <link rel="stylesheet" type="text/css" href="../css/forgotpass.css">  <!-- Include the stylesheet for the page -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">  <!-- SweetAlert2 CSS for modal dialogs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  <!-- SweetAlert2 JS for alerts -->
</head>
<body>
    <!-- Display logo and title of the system -->
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <!-- Wrapper for the forgot password form -->
    <div class="wrapper">
        <div class="login-wrapper">
            <form id="sendemail-form" action="" method="post"> 
                <!-- Instructions for the user to enter their email -->
                <p id="welcome">Forgot Password?</p>
                <p id="login2">Enter the email address you used for your account,
                    and we will send a verification code to enable you to change 
                    your password.</p>
                
                <!-- Display any session messages if they exist (success or error) -->
                <?php if (!empty($_SESSION['message']) && empty($jsScript)): ?>
                    <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                        <?= $_SESSION['message']; ?>
                    </p>
                <?php endif; ?>

                <!-- Input field for the email address -->
                <div class="form-container">
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="email" name="emaill" id="email" class="form-input" placeholder="Enter your Email" required>
                    </div>
                </div>

                <!-- Buttons for sending the email or going back to the login page -->
                <div class="buttons">
                    <button id="return" type="button" onclick="window.location.href='index.php';">Back</button>
                    <button id="sendemail" type="submit">Send</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Include JavaScript for handling the SweetAlert2 dialog if the OTP is sent successfully -->
    <?php if (!empty($jsScript)): ?>
        <script>
            <?= $jsScript; ?>
        </script>
    <?php endif; ?>    
</body>
</html>