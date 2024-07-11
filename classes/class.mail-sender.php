<?php

/**
 * This Class is responsible for sending emails using PHPMailer Library
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailSender
{

    public function send_email_quote($quote_id, $pdfFileName)
    {
        // run query method to get the latest data by id
        $quote_data = getQuoteDataById($quote_id);

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

            // SMTP server configuration
            // $mail->Host       = 'ex5.mail.ovh.net';             // SMTP host
            // $mail->SMTPAuth   = true;                           // Enable SMTP authentication
            // $mail->Username   = SMTP_USERNAME;                  // SMTP username from environment variable
            // $mail->Password   = SMTP_PASSWORD;                  // SMTP password from environment variable
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
            // $mail->Port       = 587;                            // TCP port to connect to
            // $mail->CharSet    = 'UTF-8';                        // Set character encoding to UTF-8

            // Sender information
            $mail->setFrom('contact@microzoo.fr', 'MicroZoo');

            // Set client's name
            $client = (!empty($quote_data->companyName) ? (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot)));

            // Add recipients
            $mail->addAddress($quote_data->email_quot, $client); // Client's email and name, the name is optional
            $mail->addBCC('contact@microzoo.fr', 'MicroZoo Admin'); // Admin's email

            // Add reply address
            $mail->addReplyTo('mz@example.com', 'Information');

            // Attachments
            $pdfFilePath = plugin_dir_path(__FILE__) . '../src/save/' . $pdfFileName; // Full path to the PDF file
            if (file_exists($pdfFilePath)) {
                $mail->addAttachment($pdfFilePath, $pdfFileName); // Add attachment
            } else {
                error_log('Attachment file does not exist: ' . $pdfFilePath);
                echo 'Attachment file does not exist: ' . $pdfFilePath;
            }

            // Email subject
            $mail->isHTML(true);
            $mail->Subject = 'MicroZoo devis pour ' . $client;

            ob_start(); // Start output buffering
            // Email body from a separate html file
            include(plugin_dir_path(__FILE__) . '../template/template.email.php');
            $emailBody = ob_get_clean();
            $mail->Body = $emailBody;

            ob_start(); // Start another buffer
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

    // Method for handling question form submission and sending email to admin
    public function send_email_question_to_admin($form_data)
    {

        try {
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

            // SMTP server configuration
            // $mail->Host       = 'ex5.mail.ovh.net';             // SMTP host
            // $mail->SMTPAuth   = true;                           // Enable SMTP authentication
            // $mail->Username   = SMTP_USERNAME;                  // SMTP username from environment variable
            // $mail->Password   = SMTP_PASSWORD;                  // SMTP password from environment variable
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
            // $mail->Port       = 587;                            // TCP port to connect to
            // $mail->CharSet    = 'UTF-8';                        // Set character encoding to UTF-8

            // Set email sender
            $mail->setFrom('contact@microzoo.fr', 'MicroZoo');

            // Add recipients (Admin's email)
            $mail->addAddress('contact@microzoo.fr', 'MicroZoo Admin');

            // Set email subject
            $mail->Subject = 'Question du ' . $form_data['firstname_quest'] . ' ' . $form_data['lastname_quest'] . ' (' . $form_data['email_quest'] . ') ' . 'sur le site web';

            // Construct email body
            $emailBody = "Email: " . $form_data['email_quest'] . "\n";
            $emailBody .= "Nom: " . $form_data['lastname_quest'] . "\n";
            $emailBody .= "Prénom: " . $form_data['firstname_quest'] . "\n";
            $emailBody .= "Téléphone: " . $form_data['phone_quest'] . "\n";  
            $emailBody .= "Message: " . stripslashes($form_data['message']); // stripslashes removes all slash characters
            
            // Set email body
            $mail->Body = $emailBody;

            // Set Reply-To header to null to prevent replies
            $mail->addReplyTo('', '');

            // Send email
            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $e->getMessage();
        }
    }

    // Method for handling question form submission and sending email to client
    public function send_email_question_to_client($form_data)
    {

        try {
            // Instantiate PHPMailer
            $mail = new PHPMailer();

            // Set mailer to use SMTP
            $mail->isSMTP();

            // Configure SMTP settings (MailHog)
            $mail->Host = 'localhost';
            $mail->SMTPAuth = false;
            $mail->Username = '';
            $mail->Password = '';
            $mail->Port = 1025;

            // SMTP server configuration
            // $mail->Host       = 'ex5.mail.ovh.net';             // SMTP host
            // $mail->SMTPAuth   = true;                           // Enable SMTP authentication
            // $mail->Username   = SMTP_USERNAME;                  // SMTP username from environment variable
            // $mail->Password   = SMTP_PASSWORD;                  // SMTP password from environment variable
            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption, `PHPMailer::ENCRYPTION_SMTPS` also accepted
            // $mail->Port       = 587;                            // TCP port to connect to
            // $mail->CharSet    = 'UTF-8';                        // Set character encoding to UTF-8

            // Set email sender
            $mail->setFrom('contact@microzoo.fr', 'MicroZoo');

            // Add recipients (Client's email)
            $mail->addAddress($form_data['email_quest']);

            // Set email subject with encoding
            $subject = 'Merci de votre intérêt pour MicroZoo!';
            $mail->Subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

            ob_start(); // Start another buffer
            // Email body from a separate html file
            include(plugin_dir_path(__FILE__) . '../template/template.email_client.php');
            $emailBody = ob_get_clean();
            $mail->Body = $emailBody;

            ob_start(); // Start another buffer
            // Email alt body from a separate file in plain text for non-HTML mail clients
            include(plugin_dir_path(__FILE__) . '../template/template.email_client_plaintext.php');
            $altBody = ob_get_clean();
            $mail->AltBody = $altBody;

            // Set Reply-To header to null to prevent replies
            $mail->addReplyTo('', '');

            // Send email
            $mail->send();
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $e->getMessage();
        }
    }
}
