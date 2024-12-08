<?php
session_start();

// Include necessary files for database and user management
include '../database/config.php'; 
include '../php/user.php';

// Unset any existing error messages from the session
unset($_SESSION['error_message']);

// Create a new database connection and user manager
$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$jsScript = '';  
$message = '';   
$type = '';      

// Check if the password change form has been submitted
if (isset($_POST['changed_password'])) {
    // Get the password and confirm password values from the form
    $pass = $_POST['password'];
    $confirmpass = $_POST['confirm_password'];

    // Check if the passwords match
    if ($pass !== $confirmpass) {
        $_SESSION['message'] = "Passwords do not match!";  
        $_SESSION['message_type'] = "error"; 
    }
    // Check if the password meets the required pattern (at least 8 characters, one special character)
    elseif (!preg_match('/^(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/', $pass)) {
        $_SESSION['message'] = "Password must be at least 8 characters long and contain at least one special character.";  // Error message
        $_SESSION['message_type'] = "error"; 
    }
    // If the password matches the requirements
    else {
        $email = $_SESSION['emaill']; 
        $encryptpass = password_hash($pass, PASSWORD_DEFAULT); 
        // Call the changePassword method to update the password in the database
        if ($user->changePassword($email, $encryptpass)) {
            $type = "success";  
            $jsScript = "
                document.body.classList.add('active');
                Swal.fire({
                    title: 'Password Updated!',
                    text: 'Click continue to login.',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php';  // Redirect to login page after password change
                    }
                });
            ";
        } else {
            $_SESSION['message'] = "Error updating password. Please try again.";  // Error message if password update fails
            $_SESSION['message_type'] = "error";  // Error message type
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <!-- Link to the custom CSS file for styling the page -->
    <link rel="stylesheet" type="text/css" href="../css/changepass.css">
    <!-- Link to the SweetAlert2 CSS file for pop-up messages -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Link to SweetAlert2 JavaScript library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Logo and title for the page -->
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <!-- Main container for the form -->
    <div class="wrapper">
        <div class="login-wrapper">
            <!-- Password change form -->
            <form action="" method="post">
                <input type="hidden" name="otp" value="<?php echo htmlspecialchars($otp); ?>">
                <p id="welcome">Change Password</p>
                <p id="login2">Create your new Password</p>

                <!-- Display any session message (e.g., success or error) -->
                <?php if (isset($_SESSION['message'])): ?>
                    <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                        <?= $_SESSION['message']; ?>
                    </p>
                    <?php unset($_SESSION['message']); ?>  <!-- Clear the session message after displaying -->
                <?php endif; ?>

                <!-- Form fields for the password change -->
                <div class="form-container">
                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <img src="../assets/img/password.png" alt="password icon">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Enter your new Password" required>
                        <input type="checkbox" id="show-password"> <!-- Checkbox to toggle password visibility -->
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <img src="../assets/img/password.png" alt="password icon">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-input" placeholder="Confirm Password" required>
                        <input type="checkbox" id="show1"> <!-- Checkbox to toggle confirm password visibility -->
                    </div>
                </div>

                <!-- Submit button to trigger password change -->
                <button type="submit" id="loginbtn" name="changed_password">Submit</button>

                <!-- Link to go back to the login page -->
                <div class="back-to-login">
                    <img src="../assets/img/back.png" alt="Back icon">
                    <a href="index.php" id="backlogin">Back to Login Page</a>
                </div>
            </form>
        </div>
    </div>

    <!-- If there is a custom JavaScript (for SweetAlert), it will be injected here -->
    <?php if (isset($jsScript)): ?>
        <script>
            <?php echo $jsScript; ?>
        </script>
    <?php endif; ?>

    <!-- Script to toggle password visibility for the 'password' field -->
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

    <!-- Script to toggle password visibility for the 'confirm_password' field -->
    <script>
        document.getElementById('show1').addEventListener('change', function() {
            const passwordField = document.getElementById('confirm_password');
            if (this.checked) {
                passwordField.type = 'text'; 
            } else {
                passwordField.type = 'password'; 
            }
        });
    </script>
</body>
</html>