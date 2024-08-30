<?php
// Include your database connection if you need to retrieve student info
include("../LoginRegisterAuthentication/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$student_id = isset($_GET['id']) ? $_GET['id'] : '';

if ($student_id) {
    // Retrieve student's email and other information from the database
    $query = "SELECT * FROM students WHERE id = $student_id";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'ramaninyo5@gmail.com'; // Your email
            $mail->Password   = 'drnk ubfg kdcr npfd'; // Your email password
            $mail->SMTPSecure = 'ssl'; // Use 'tls' with port 587 if necessary
            $mail->Port       = 465;

            // Recipients
            $mail->setFrom('ramaninyo5@gmail.com', 'Your School Name');
            $mail->addAddress($student['email'], $student['firstname'] . ' ' . $student['lastname']);

            // Attachments (optional)
            // Uncomment the next line to add an attachment
            // $mail->addAttachment('/path/to/attachment.pdf'); // Add your attachment here

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Final Grade';
            $mail->Body    = 'Dear ' . $student['firstname'] . ',<br><br>Please find the attached document for your final grade.';

            $mail->send();
            echo "<script>
                    alert('Message has been sent');
                    document.location.href = 'index.php';
                  </script>";
        } catch (Exception $e) {
            echo "<script>
                    alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
                    document.location.href = 'index.php';
                  </script>";
        }
    } else {
        echo "<script>
                alert('Student not found.');
                document.location.href = 'index.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid student ID.');
            document.location.href = 'index.php';
          </script>";
}
?>
