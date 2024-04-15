<?php

/**
 * This Class is responsible for sending emails using PHPMailer Library
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailSender
{

    public function send_email_quote($quote_data)
    {
        try {
            // Instantiate PHPMailer
            $mail = new PHPMailer();

            // Set mailer to use SMTP
            $mail->isSMTP();

            // MailHog configuration
            $mail->Host = '127.0.0.1';
            $mail->SMTPAuth = false;
            $mail->Username = '';
            $mail->Password = '';
            $mail->Port = 1025;

            // SMTP configuration
            // $mail->Host       = 'smtp.example.com';  // SMTP host
            // $mail->SMTPAuth   = true;                 // Enable SMTP authentication
            // $mail->Username   = 'your@example.com';   // SMTP username
            // $mail->Password   = 'your_password';      // SMTP password
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
            // $mail->Port       = 587;                  // TCP port to connect to

            // Sender information
            $mail->setFrom('microzoo@example.com', 'Micro Zoo');

            // Add recipients
            $mail->addAddress('client@example.com', 'Client Example'); // Client's email, name is optional
            $mail->addBCC('microzoo@example.com'); // Admin's email

            // Add reply address
            $mail->addReplyTo('mz@example.com', 'Information');

            // Attachments
            $pdfFileName = 'devis_microzoo_' . $quote_data->number_quote . '.pdf'; // Custom PDF file name
            $pdfFilePath = plugin_dir_path(__FILE__) . '../src/save/' . $pdfFileName; // Full path to the PDF file
            if (file_exists($pdfFilePath)) {
                $mail->addAttachment($pdfFilePath, $pdfFileName); // Add attachment
            } else {
                echo 'Attachment file does not exist: ' . $pdfFilePath;
            }

            // var_dump($quote_data->number_quote);
            // var_dump($pdfFilePath);
            // die();

            // Email subject
            $mail->isHTML(true);
            $client = (!empty($quote_data->companyName) ? (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot)));
            $mail->Subject = 'MicroZoo devis pour ' . $client;

            // Email body from a separate html file
            include(plugin_dir_path(__FILE__) . '../template/template.email.php');
            $emailBody = ob_get_clean();
            $mail->Body = $emailBody;

            // Email alt body from a separate file in plain text for non-HTML mail clients
            include(plugin_dir_path(__FILE__) . '../template/template.email_plaintext.php');
            $altBody = ob_get_clean();
            $mail->AltBody = $altBody;

            // Send email
            $mail->send();

        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $e->getMessage();
        }
    }

    // Method for handling question form submissions and sending emails
    public function send_email_question($form_data)
    {

        // Instantiate PHPMailer
        $mail = new PHPMailer();

        // Set mailer to use SMTP
        $mail->isSMTP();

        // Configure SMTP settings (MailHog or any other SMTP server)
        $mail->Host = 'localhost';
        $mail->SMTPAuth = false;
        $mail->Username = '';
        $mail->Password = '';
        $mail->Port = 1025;

        // Set email sender
        $mail->setFrom('mz@example.com', 'MicroZoo Admin');

        // Add recipients (Admin's email)
        $mail->addAddress('microzoo@example.com');

        // Set email subject
        $mail->Subject = 'Question from Website';

        // Construct email body
        $emailBody = "Email: " . $form_data['email_quest'] . "\n";
        $emailBody .= "Nom: " . $form_data['lastname_quest'] . "\n";
        $emailBody .= "Prénom: " . $form_data['firstname_quest'] . "\n";
        $emailBody .= "Téléphone: " . $form_data['phone_quest'] . "\n";
        $emailBody .= "Message: " . $form_data['message'];

        // Set email body
        $mail->Body = $emailBody;

        // Send email
        if (!$mail->send()) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo 'Email has been sent successfully';
        }
    }
}
