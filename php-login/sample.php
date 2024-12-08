<?php
// $hashedPassword represents the hashed password stored in the database (typically obtained using password_hash())
$hashedPassword = '$2y$10$os.yczuFLfvMps91e34K/eQYj4To144F.kv4e3TBg5XAwZB9.BLTO';

// $inputPassword is the password entered by the user (in this case, '2210' is used for testing)
$inputPassword = '2210';

// password_verify is a built-in PHP function that checks if the entered password matches the hashed password
if (password_verify($inputPassword, $hashedPassword)) {
    // If the password is valid (matches the hash), this message is displayed
    echo 'Password is valid!';
} else {
    // If the password does not match the hash, this message is displayed
    echo 'Invalid password.';
}
?>