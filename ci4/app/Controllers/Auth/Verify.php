<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class Verify extends BaseController
{
    public function index()
    {
        $token = $this->request->getGet('token');
        $email = $this->request->getGet('email');

        if (empty($token) || empty($email)) {
            return view('auth/verify', ['status' => 'error', 'message' => 'Invalid verification link.']);
        }

        $userModel = model('UserClientModel');
        $user = $userModel->where('email', urldecode($email))->where('verification_token', $token)->first();

        if ($user) {
            $userModel->update($user['client_id'], [
                'Status'             => 1,
                'verification_token' => null,
            ]);
            return view('auth/verify', ['status' => 'success', 'message' => 'Email verified successfully! You can now log in.']);
        }

        return view('auth/verify', ['status' => 'error', 'message' => 'Invalid or expired verification token.']);
    }
}
