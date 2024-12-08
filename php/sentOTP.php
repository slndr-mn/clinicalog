<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception; 
     
class sentOTP {
    private $mail;

    public function __construct() {
        // Create a new PHPMailer instance 
        $this->mail = new PHPMailer(true);
        
        // Set up SMTP settings
        $this->mail->isSMTP();
        $this->mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $this->mail->SMTPAuth = true;  // Enable SMTP authentication
        $this->mail->Username = 'casiagwynethmarie@gmail.com';  // SMTP username from environment variable
        $this->mail->Password = 'jhcf bipv lbbd pizm';  // SMTP password from environment variable
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption, `ssl` also accepted
        $this->mail->Port = 587;  // TCP port to connect to
        $this->mail->setFrom('casiagwynethmarie@gmail.com', 'OTP Sender');
    }

    public function sendOtp($recipientEmail, $otp) {
        try {
            $this->mail->addAddress($recipientEmail);  // Add a recipient
            $this->mail->isHTML(true);  // Set email format to HTML
            $this->mail->Subject = 'One Time Password - OTP';
            $this->mail->Body = 'Hi, here is your One Time Password <br/><b>' . $otp . '</b>';

            $this->mail->send();
            return ['success' => true, 'message' => 'OTP sent successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Mailer Error: ' . $this->mail->ErrorInfo];
        }
    }
}
?>
