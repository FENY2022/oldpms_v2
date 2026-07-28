<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Libraries\EmailService;
use App\Libraries\FileUploadService;

class Register extends BaseController
{
    public function index()
    {
        if ($this->request->is('post')) {
            $fname = htmlspecialchars($this->request->getPost('firstname') ?? '');
            $mname = htmlspecialchars($this->request->getPost('mid_name') ?? '');
            $lname = htmlspecialchars($this->request->getPost('lastname') ?? '');
            $email = filter_var($this->request->getPost('email') ?? '', FILTER_SANITIZE_EMAIL);
            $unhashedPassword = $this->request->getPost('password') ?? '';
            $confirmPassword = $this->request->getPost('confirm_password') ?? '';
            $mobile = htmlspecialchars($this->request->getPost('mobilenum') ?? '');
            $province = htmlspecialchars($this->request->getPost('province') ?? '');
            $citymun = htmlspecialchars($this->request->getPost('citymun') ?? '');
            $brgy = htmlspecialchars($this->request->getPost('brgy') ?? '');
            $zips = htmlspecialchars($this->request->getPost('zips') ?? '');

            // Password strength check
            $score = 0;
            if (strlen($unhashedPassword) >= 8) $score++;
            if (preg_match('@[a-z]@', $unhashedPassword)) $score++;
            if (preg_match('@[A-Z]@', $unhashedPassword)) $score++;
            if (preg_match('@[0-9]@', $unhashedPassword)) $score++;
            if (preg_match('@[^\w]@', $unhashedPassword)) $score++;

            if ($unhashedPassword !== $confirmPassword) {
                return redirect()->to(base_url('/'))->withInput()->with('error', 'Passwords do not match.');
            } elseif (strlen($unhashedPassword) < 8) {
                return redirect()->to(base_url('/'))->withInput()->with('error', 'Password must be at least 8 characters.');
            } elseif ($score < 3) {
                return redirect()->to(base_url('/'))->withInput()->with('error', 'Password is too weak. Please use a stronger password.');
            }

            // Check email exists
            $userModel = model('UserClientModel');
            if ($userModel->where('email', $email)->countAllResults() > 0) {
                return redirect()->to(base_url('/'))->withInput()->with('error', 'Email already registered. Please use a different email.');
            }

            // Handle file uploads
            $uploadService = new FileUploadService();
            $compIdPath = $uploadService->uploadFile('comp_id_upload', 'uploads/');
            $govtIdPath = $uploadService->uploadFile('govt_id_upload', 'uploads/');
            $authLetterPath = $uploadService->uploadFile('auth_letter', 'uploads/');

            $verificationToken = bin2hex(random_bytes(32));

            $userData = [
                'firstname'          => $fname,
                'mid_name'           => $mname,
                'lastname'           => $lname,
                'email'              => $email,
                'verification_token' => $verificationToken,
                'password'           => password_hash($unhashedPassword, PASSWORD_DEFAULT),
                'mobilenum'          => $mobile,
                'comp_id_upload'     => $compIdPath ?? '',
                'govt_id_upload'     => $govtIdPath ?? '',
                'auth_letter'        => $authLetterPath ?? '',
                'password_unhashed'  => $unhashedPassword,
                'Status'             => 0,
                'province'           => $province,
                'citymun'            => $citymun,
                'brgy'               => $brgy,
                'zips'               => $zips,
            ];

            if ($userModel->insert($userData)) {
                // Send verification email
                $emailService = new EmailService();
                $verifyLink = base_url("/verify?token=$verificationToken&email=" . urlencode($email));
                $emailService->sendVerificationEmail($email, $fname, $verifyLink);

                return redirect()->to(base_url('/'))->with('success', 'Registration Successful! Check your email for the verification link.');
            }

            return redirect()->to(base_url('/'))->withInput()->with('error', 'Registration Failed. Please try again.');
        }

        return redirect()->to(base_url('/'));
    }
}
