<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\EmailService;

class ForgotPassword extends BaseController
{
    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $userModel = model('UserClientModel');
            $user = $userModel->where('email', $email)->first();

            if ($user) {
                $token = bin2hex(random_bytes(32));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

                $passwordResetModel = model('PasswordResetModel');
                // Delete any existing tokens for this email
                $passwordResetModel->where('email', $email)->delete();
                $passwordResetModel->insert([
                    'email'      => $email,
                    'token'      => $token,
                    'expires_at' => $expiresAt,
                ]);

                $emailService = new EmailService();
                $resetLink = base_url("/reset-password?token=$token&email=" . urlencode($email));
                $emailService->sendPasswordResetEmail($email, $resetLink);
            }

            // Always show same message for security
            return view('auth/forgot_password', ['sent' => true]);
        }

        return view('auth/forgot_password', ['sent' => false]);
    }
}
