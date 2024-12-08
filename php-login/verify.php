<?php
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache");
header("Expires: 0");

session_start();

// Include necessary files for database connection, user-related functionalities, and OTP sending
include '../database/config.php';  
include '../vendor/autoload.php';  
include '../php/user.php';  
include '../php/sentOTP.php';  

// Create a new instance of the database connection
$database = new Database();
$db = $database->getConnection();  // Get the database connection

// Create a new instance of the User class
$user = new User($db);

// Variable to hold JavaScript for displaying SweetAlert after OTP verification
$jsScript = '';

// Check if the form was submitted via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // If the OTP is provided in the POST data
    if (isset($_POST['otp'])) {
        // Sanitize and retrieve the OTP and email stored in session
        $otp = trim($_POST['otp']);
        $email = $_SESSION['emaill'] ?? '';  

        // Verify OTP if both email and OTP are available
        if (!empty($email) && !empty($otp)) {
            if ($user->verifyOtp($email, $otp)) {  
                $_SESSION['emaill'] = $email;  
                $jsScript = "
                    // Display SweetAlert on successful OTP verification
                    document.body.classList.add('active');
                    Swal.fire({
                        title: 'Verified Successfully!',
                        text: 'Please press continue to change password',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'changepass.php';  // Redirect to password change page
                        }
                    });
                ";
            } else {
                // Show error message if OTP is incorrect
                $_SESSION['message'] = "Invalid OTP. Please try again.";
                $_SESSION['message_type'] = "error";  // Set message type as error
            }
        } else {
            // Show error if OTP or email is missing
            $_SESSION['message'] = "Please enter the OTP.";
            $_SESSION['message_type'] = "error";
        }
    } 
    // If the user requests to resend the OTP
    elseif (isset($_POST['resend']) && $_POST['resend'] === 'check') {
        $email = $_SESSION['emaill'] ?? ''; 

        // Validate email format
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $otp = random_int(100000, 999999);  

            // Check if the email exists in the system
            if ($user->emailverify($email)) {
                if ($user->updateCode($email, $otp)) {  
                    // Create an instance of the sentOTP class to send the OTP
                    $emailSender = new sentOTP();
                    $emailResult = $emailSender->sendOtp($email, $otp);

                    if ($emailResult['success']) {  
                        $_SESSION['emaill'] = $email;  
                        $_SESSION['message'] = "New code sent!"; 
                        $_SESSION['message_type'] = "success"; 
                    } else {
                        // Error occurred while sending OTP
                        $_SESSION['message'] = "An error occurred while sending the OTP.";
                        $_SESSION['message_type'] = "error";
                    }
                } else {
                    // Error while updating OTP in the database
                    $_SESSION['message'] = "Error updating OTP.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                // Email not found in the system
                $_SESSION['message'] = "Email not found. Please try again.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            // Invalid email format
            $_SESSION['message'] = "Invalid email address provided.";
            $_SESSION['message_type'] = "error";
        }

        // Return the message as a JSON response for AJAX handling
        echo json_encode([
            'message' => $_SESSION['message'] ?? '',
            'message_type' => $_SESSION['message_type'] ?? ''
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="stylesheet" type="text/css" href="../css/verify.css">  <!-- CSS for styling the verification page -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"> <!-- SweetAlert2 library for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>  <!-- SweetAlert2 script -->
</head>

<body>
    <!-- Displaying the logo and page name -->
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <div class="wrapper">
        <div class="login-wrapper">
            <!-- Form for OTP verification -->
            <form action="verify.php" method="POST" autocomplete="off">
                <p id="welcome">Verification</p>
                <p id="login2">Enter the code sent through your email. </p>

                <!-- Display error or success messages if available -->
                <?php if (isset($_SESSION['message'])): ?>
                    <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                        <?= $_SESSION['message']; ?>
                    </p>
                    <?php unset($_SESSION['message']); ?>  <!-- Remove message after display -->
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-group">
                        <!-- Input field for OTP -->
                        <label for="otp" class="form-label">Code:</label>
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="text" name="otp" id="otp" class="form-input" placeholder="Enter Code">
                        <span class="right-placeholder" id="countdown"></span>  <!-- Countdown for resend -->
                    </div>

                    <!-- Section to show resend link -->
                    <div class="resend">
                        <span id="countdown"></span>
                        <span id="click"><a href="#" id="resend-link" onclick="resendCode(); return false;">Resend Code</a></span>
                    </div>
                </div>

                <button type="submit" id="sendemail" name="sendotp">Continue</button>  <!-- Submit button -->
            </form>

            <!-- Link to go back to the login page -->
            <div class="back-to-login">
                <img src="../assets/img/back.png" alt="Back icon">
                <a href="index.php" id="backlogin">Back to Login Page</a>
            </div>
        </div>
    </div>

    <!-- Display JavaScript if available -->
    <?php if (!empty($jsScript)): ?>
        <script>
            <?= $jsScript; ?>
        </script>
    <?php endif; ?>

    <script>
        // Function to handle OTP resending
        function resendCode() {
            // Send POST request to the same page to trigger OTP resend
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'resend=check' 
                })
                .then(response => response.json()) 
                .then(data => {
                    // Show the message returned from the server
                    const messageElement = document.getElementById('error-message');
                    messageElement.innerText = data.message;
                    messageElement.style.color = data.message_type === 'success' ? 'green' : 'red';
                });
            startCountdown();  // Start countdown for resend link
        }

        // Function to start the countdown timer for OTP resend
        function startCountdown() {
            let countdown = 20;  // 20 seconds countdown
            const resendLink = document.getElementById('resend-link');
            resendLink.style.pointerEvents = 'none';  // Disable resend link during countdown

            const countdownDisplay = document.getElementById('countdown');
            countdownDisplay.innerText = `${countdown} seconds`;

            const countdownTimer = setInterval(function() {
                countdown--;
                countdownDisplay.innerText = `${countdown} seconds`;
                if (countdown <= 0) {
                    clearInterval(countdownTimer);  // Clear interval when countdown reaches zero
                    resendLink.style.pointerEvents = 'auto';  // Re-enable the resend link
                    countdownDisplay.innerText = '';  // Clear countdown display
                }
            }, 1000);  // Update every second
        }
    </script>

</body>

</html>