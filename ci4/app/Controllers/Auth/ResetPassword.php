<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class ResetPassword extends BaseController
{
    public function index()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');

        if ($this->request->is('post')) {
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password');
            $email = $this->request->getPost('email');
            $token = $this->request->getPost('token');

            if ($password !== $confirmPassword) {
                return view('auth/reset_password', ['token' => $token, 'email' => $email, 'error' => 'Passwords do not match.']);
            }

            if (strlen($password) < 8) {
                return view('auth/reset_password', ['token' => $token, 'email' => $email, 'error' => 'Password must be at least 8 characters.']);
            }

            $passwordResetModel = model('PasswordResetModel');
            $resetRecord = $passwordResetModel->where('email', $email)->where('token', $token)->where('expires_at >', date('Y-m-d H:i:s'))->first();

            if (!$resetRecord) {
                return view('auth/reset_password', ['token' => $token, 'email' => $email, 'error' => 'Invalid or expired reset token.']);
            }

            $userModel = model('UserClientModel');
            $user = $userModel->where('email', $email)->first();

            if ($user) {
                $userModel->update($user['client_id'], [
                    'password'          => password_hash($password, PASSWORD_DEFAULT),
                    'password_unhashed' => $password,
                ]);
                $passwordResetModel->where('email', $email)->delete();
                return view('auth/reset_password', ['token' => null, 'email' => null, 'success' => 'Password reset successful! You can now log in.']);
            }

            return view('auth/reset_password', ['token' => $token, 'email' => $email, 'error' => 'User not found.']);
        }

        if (empty($token) || empty($email)) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid reset link.');
        }

        $passwordResetModel = model('PasswordResetModel');
        $validToken = $passwordResetModel->where('email', $email)->where('token', $token)->where('expires_at >', date('Y-m-d H:i:s'))->first();

        if (!$validToken) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid or expired reset token.');
        }

        return view('auth/reset_password', ['token' => $token, 'email' => $email, 'error' => null, 'success' => null]);
    }
}
