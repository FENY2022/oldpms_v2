<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $appModel = model('PermitApplicationModel');

        $totalApps = $appModel->countAllResults();
        $pendingApps = $appModel->whereIn('status', ['Under Evaluation', 'Pending Review'])->countAllResults();
        $approvedApps = $appModel->whereIn('status', ['Approved', 'Issued', 'Completed'])->countAllResults();

        $notifications = $appModel->whereIn('status', ['Under Evaluation', 'Pending Review'])->orderBy('date_submitted', 'DESC')->findAll();

        $userRoleId = (string)session()->get('user_role_id');

        $data = [
            'totalApps'      => $totalApps,
            'pendingApps'    => $pendingApps,
            'approvedApps'   => $approvedApps,
            'notifications'  => $notifications,
            'userRoleId'     => $userRoleId,
        ];

        return view('admin/dashboard', $data);
    }
}
