<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if (isset($_POST['send'])) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ramaninyo5@gmail.com';
        $mail->Password = 'drnk ubfg kdcr npfd';  // Consider using environment variables
        $mail->SMTPSecure = 'ssl';  // Use 'tls' with port 587 if necessary
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('ramaninyo5@gmail.com', 'GradingManagementSystemNoReply');
        $mail->addAddress($_POST['email']);  // Add recipient email

        // Content
        $mail->isHTML(true);  // Set email format to HTML
        $mail->Subject = $_POST['subject'];
        $mail->Body    = $_POST['message'];

        // Attachment
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
            $mail->addAttachment($_FILES['attachment']['tmp_name'], $_FILES['attachment']['name']);
        }

        $mail->send();
        echo "<script>
                alert('Sent Successfully');
                document.location.href = 'index.php';
              </script>";
    } catch (Exception $e) {
        echo "<script>
                alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
                document.location.href = 'index.php';
              </script>";
    }
}
?>
