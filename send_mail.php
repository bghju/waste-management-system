<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// __DIR__ safely locks in the exact path you showed me in your screenshot
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

function sendWasteAlert($block, $category, $qty) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        $mail->Username = "ajayaravinth50@gmail.com";
        // REMEMBER: Put your newly generated 16-letter App Password here
        $mail->Password = "zwfzlwjugcvgxder"; 

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom("ajayaravinth50@gmail.com", "Waste Management System");
        $mail->addAddress("ajayaravinth50@gmail.com");

        $mail->isHTML(true);
        $mail->Subject = "Waste Marked OUT Notification";

        $mail->Body = "
        <h3>Waste Removed From Campus</h3>
        <b>Block:</b> $block <br>
        <b>Waste Type:</b> $category <br>
        <b>Quantity:</b> $qty kg
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        echo "Mail Error: " . $mail->ErrorInfo;
        return false;
    }
}
?>