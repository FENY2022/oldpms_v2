<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

class Login extends BaseController
{
    public function index()
    {
        if ($this->request->is('post')) {
            $this->response->setHeader('Content-Type', 'application/json');

            $loginId = trim($this->request->getPost('email'));
            $password = $this->request->getPost('password');

            if (empty($loginId) || empty($password)) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Please enter both email/username and password.']);
            }

            // 1. Check Client
            $userModel = model('UserClientModel');
            $user = $userModel->where('email', $loginId)->first();

            if ($user && password_verify($password, $user['password'])) {
                if ($user['Status'] == 0) {
                    return $this->response->setJSON(['status' => 'error', 'message' => 'Your account is not verified yet. Please check your email to verify your account.']);
                }

                session()->set([
                    'client_id'  => $user['client_id'],
                    'firstname'  => $user['firstname'],
                    'lastname'   => $user['lastname'],
                    'email'      => $user['email'],
                    'user_type'  => 'client',
                    'logged_in'  => true,
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'Client login successful! Redirecting...', 'redirect' => '/client/dashboard']);
            }

            // 2. Check DENR User
            $denrModel = model('DenrUserModel');
            $denrUser = $denrModel->where('username', $loginId)->first();

            if ($denrUser && password_verify($password, $denrUser['password'])) {
                session()->set([
                    'user_id'       => $denrUser['user_id'],
                    'name'          => $denrUser['name'],
                    'username'      => $denrUser['username'],
                    'usertype'      => $denrUser['usertype'],
                    'office_id'     => $denrUser['office_id'],
                    'user_role_id'  => $denrUser['user_role_id'],
                    'user_type'     => 'denr_user',
                    'logged_in'     => true,
                ]);

                return $this->response->setJSON(['status' => 'success', 'message' => 'DENR Staff login successful! Redirecting...', 'redirect' => '/admin/dashboard']);
            }

            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid email/username or password.']);
        }

        return redirect()->to('/');
    }
}
