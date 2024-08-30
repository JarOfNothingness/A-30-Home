<?php
// Include your database connection if you need to retrieve student info
include("../LoginRegisterAuthentication/connection.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php'; // Make sure Composer autoload is required
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['send'])){
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'ramaninyo5@gmail.com'; //gmail user
    $mail->Password = 'drnk ubfg kdcr npfd'; //gmail app password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('ramaninyo5@gmail.com');

    $mail->addAddress($_POST['email']);

    $mail->isHTML(true);

    $mail->Subject = $_POST['subject'];
    $mail->Body = $_POST['message'];

    $mail->send();

    echo
    "
    <script>
    alert('Sent Succesfully');
    document.location.href = 'r.php';
    </script>
    ";


}
?>