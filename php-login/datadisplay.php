<?php
session_start();

// Include necessary files for database connection and user management
include('../database/config.php');  
include('../php/user.php');         

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); 
    exit; 
}

// Create a new database connection
$db = new Database();
$conn = $db->getConnection();

// Instantiate the User class to interact with user data
$user = new User($conn);

// Get the user ID number from the session (it should be set during login)
$user_idnum = $_SESSION['user_idnum'];

// Fetch the user's data based on the user ID number
$userData = $user->getUserData($user_idnum);  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>User Profile</title>  
</head>
<body>
    <!-- Display the user ID number (just for debugging or informational purposes) -->
    <p><?php echo $user_idnum; ?></p>
    
    <!-- Check if user data was retrieved successfully -->
    <?php if ($userData): ?>
    <!-- If user data is found, display it in a table -->
    <table border="1">
        <thead>
            <tr>
                <th>Profile</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Date Added</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- Display the user's profile picture with a circular crop (50px x 50px) -->
                <td><img src='/php-admin/uploads/<?php echo htmlspecialchars($userData['user_profile']); ?>' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'></td>
                
                <!-- Display the user's ID number -->
                <td><?php echo htmlspecialchars($userData['user_idnum']); ?></td>
                
                <!-- Display the user's full name by combining last, first, and middle names -->
                <td><?php echo htmlspecialchars($userData['user_lname']) . ', ' . htmlspecialchars($userData['user_fname']) . ' ' . htmlspecialchars($userData['user_mname']); ?></td>
                
                <!-- Display the user's email address -->
                <td><?php echo htmlspecialchars($userData['user_email']); ?></td>
                
                <!-- Display the user's position in the organization -->
                <td><?php echo htmlspecialchars($userData['user_position']); ?></td>
                
                <!-- Display the date the user was added to the system -->
                <td><?php echo htmlspecialchars($userData['user_dateadded']); ?></td>
                
                <!-- Display the current status of the user (e.g., active or inactive) -->
                <td><?php echo htmlspecialchars($userData['user_status']); ?></td>
            </tr>
        </tbody>
    </table>
    <?php else: ?>
        <!-- If no user data is found, show a message indicating so -->
        <p>User data not found.</p>
    <?php endif; ?>
</body>
</html>