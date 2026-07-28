<?php

namespace App\Libraries;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = 'venzonanthonie@gmail.com';
        $this->mail->Password   = 'irsw yeav xgqy rmll';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;
        $this->mail->setFrom('venzonanthonie@gmail.com', 'DENR O-LDPMS System');
    }

    public function sendVerificationEmail(string $to, string $firstName, string $verifyLink): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Verify Your O-LDPMS Account';
            $this->mail->Body = "<h3>Hello $firstName,</h3>
                <p>Your registration was successful. Please click the link below to verify your email address:</p>
                <p><a href='$verifyLink'>$verifyLink</a></p>
                <p>Thank you!</p>";
            $this->mail->AltBody = "Hello $firstName, please verify your email: $verifyLink";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendPasswordResetEmail(string $to, string $resetLink): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'O-LDPMS Password Reset Request';
            $this->mail->Body = "<h3>Password Reset</h3>
                <p>You requested a password reset. Click the link below to reset your password:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you did not request this, please ignore this email.</p>";
            $this->mail->AltBody = "Password reset link: $resetLink (expires in 1 hour)";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendContactAcknowledgement(string $to, string $name, string $subject): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = "O-LDPMS Acknowledgement: $subject";
            $this->mail->Body = "<h3>Hello $name,</h3>
                <p>Thank you for reaching out. We have received your message regarding '$subject' and will get back to you shortly.</p>
                <p>Best regards,<br>O-LDPMS Team</p>";
            $this->mail->AltBody = "Hello $name, we have received your message and will get back to you shortly.";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendApplicationReturnedEmail(string $to, string $firstName, string $appId, string $remarks): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->isHTML(true);
            $this->mail->Subject = "Application Returned - Action Required (App #$appId)";
            $this->mail->Body = "<div style='font-family: sans-serif; color: #333;'>
                <h3>Hello $firstName,</h3>
                <p>Your permit application <strong>#$appId</strong> has been <strong>Returned</strong> by the evaluating officer.</p>
                <div style='background-color: #ffeaea; padding: 15px; border-left: 4px solid #f44336; margin: 20px 0;'>
                    <strong>Remarks / Required Action:</strong><br/>
                    " . nl2br(htmlspecialchars($remarks)) . "
                </div>
                <p>Please log in to your O-LDPMS account to view which specific documents were tagged as incorrect and update your application.</p>
                <p>Thank you,<br>DENR O-LDPMS Team</p>
            </div>";
            $this->mail->AltBody = "Hello $firstName, your permit application #$appId has been Returned. Reason: $remarks. Please log in to fix.";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function sendApplicationResubmittedEmail(string $to, string $applicantName, string $appId): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to, 'Admin User');
            $this->mail->isHTML(true);
            $this->mail->Subject = "Application Resubmitted (App #$appId)";
            $this->mail->Body = "<div style='font-family: sans-serif; color: #333;'>
                <h3>Hello Admin,</h3>
                <p>The applicant <strong>$applicantName</strong> has successfully fixed their documents and resubmitted their permit application <strong>#$appId</strong>.</p>
                <p>The application is now marked as <strong>Under Evaluation</strong>. Please log in to the O-LDPMS Admin Portal to review the updated documents.</p>
                <p>Thank you,<br>DENR O-LDPMS System</p>
            </div>";
            $this->mail->AltBody = "Hello Admin, an applicant has resubmitted their permit application #$appId. Status is now Under Evaluation.";
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
