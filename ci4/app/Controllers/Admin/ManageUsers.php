<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ManageUsers extends BaseController
{
    public function index()
    {
        $denrUserModel = model('DenrUserModel');
        $roleModel = model('DenrRoleModel');

        if ($this->request->is('post')) {
            $userId = $this->request->getPost('user_id');
            $newRole = $this->request->getPost('user_role_id');
            $newPassword = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirm_password') ?? '';

            $updateData = ['user_role_id' => $newRole];

            if (!empty($newPassword)) {
                if ($newPassword !== $confirmPassword) {
                    return redirect()->back()->with('error', 'Passwords do not match.');
                }
                $uppercase = preg_match('@[A-Z]@', $newPassword);
                $lowercase = preg_match('@[a-z]@', $newPassword);
                $number = preg_match('@[0-9]@', $newPassword);
                $special = preg_match('@[^\w]@', $newPassword);

                if (!$uppercase || !$lowercase || !$number || !$special || strlen($newPassword) < 8) {
                    return redirect()->back()->with('error', 'Password does not meet strength requirements.');
                }

                $updateData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
                $updateData['unhashPassword'] = $newPassword;
            }

            // Handle signature upload
            $sigFile = $this->request->getFile('profile_pic');
            if ($sigFile && $sigFile->isValid() && !$sigFile->hasMoved()) {
                $dir = WRITEPATH . '../public/uploads/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);
                $newName = time() . '_' . $sigFile->getRandomName();
                $sigFile->move($dir, $newName);
                $updateData['uploadSignature'] = 'uploads/' . $newName;
            }

            $denrUserModel->update($userId, $updateData);
            return redirect()->back()->with('success', 'User successfully updated!');
        }

        $roles = $roleModel->findAll();
        $roleMap = [];
        foreach ($roles as $r) {
            $roleMap[$r['role_id']] = "[{$r['office_level']}] {$r['role_name']}";
        }
        $roleMap['Admin'] = "[ADMIN] System Admin (Legacy)";

        $users = $denrUserModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'users'    => $users,
            'roles'    => $roles,
            'roleMap'  => $roleMap,
        ];

        return view('admin/manage_users', $data);
    }
}
