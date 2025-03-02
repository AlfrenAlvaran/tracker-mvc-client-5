<?php

namespace Tracker\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['SMTP_HOST'];
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['SMTP_USERNAME'];
            $this->mailer->Password = $_ENV['SMTP_PASSWORD'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $_ENV['SMTP_PORT'];

            // Validate and set email addresses
            $emailFrom = filter_var($_ENV['EMAIL_FROM'], FILTER_VALIDATE_EMAIL);
            $emailTo = filter_var($_ENV['EMAIL_TO'], FILTER_VALIDATE_EMAIL);

            if (!$emailFrom || !$emailTo) {
                throw new Exception("Invalid email configuration: FROM ($emailFrom) or TO ($emailTo) is incorrect.");
            }

            $this->mailer->setFrom($emailFrom, 'Task Tracker');
            $this->mailer->addAddress($emailTo);
        } catch (Exception $e) {
            die("Mailer Error: " . $e->getMessage());
        }
    }

    public function sendReminderEmail($subject, $body)
    {
        try {
            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            if (!$this->mailer->send()) {
                throw new Exception("Mailer Error: " . $this->mailer->ErrorInfo);
            }

            return true;
        } catch (Exception $e) {
            die("Error sending email: " . $e->getMessage());
        }
    }
}
