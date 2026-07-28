<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;

class Profile extends BaseController
{
    public function index()
    {
        $clientId = session()->get('client_id');
        $userModel = model('UserClientModel');

        if ($this->request->getMethod() === 'post') {
            $firstname = htmlspecialchars($this->request->getPost('firstname'));
            $midName = htmlspecialchars($this->request->getPost('mid_name'));
            $lastname = htmlspecialchars($this->request->getPost('lastname'));
            $email = filter_var($this->request->getPost('email'), FILTER_SANITIZE_EMAIL);
            $mobile = htmlspecialchars($this->request->getPost('mobilenum'));

            $updateData = [
                'firstname' => $firstname,
                'mid_name'  => $midName,
                'lastname'  => $lastname,
                'email'     => $email,
                'mobilenum' => $mobile,
            ];

            // Handle profile picture
            $profilePic = $this->request->getFile('profile_picture');
            if ($profilePic && $profilePic->isValid() && !$profilePic->hasMoved()) {
                $dir = WRITEPATH . '../public/uploads/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $newName = 'profile_' . $clientId . '_' . time() . '.' . $profilePic->getExtension();
                $profilePic->move($dir, $newName);
                $updateData['profile_picture'] = 'uploads/' . $newName;
            }

            // Handle password change
            $newPassword = $this->request->getPost('new_password');
            $confirmPassword = $this->request->getPost('confirm_password');
            $currentPassword = $this->request->getPost('current_password');

            if (!empty($newPassword)) {
                $user = $userModel->find($clientId);
                if (!password_verify($currentPassword, $user['password'])) {
                    return redirect()->back()->with('error', 'Current password is incorrect.');
                }
                if ($newPassword !== $confirmPassword) {
                    return redirect()->back()->with('error', 'New passwords do not match.');
                }
                if (strlen($newPassword) < 8) {
                    return redirect()->back()->with('error', 'Password must be at least 8 characters.');
                }
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateData['password_unhashed'] = $newPassword;
            }

            $userModel->update($clientId, $updateData);

            // Update session
            session()->set([
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully!');
        }

        $user = $userModel->find($clientId);
        return view('client/profile', ['user' => $user]);
    }
}
