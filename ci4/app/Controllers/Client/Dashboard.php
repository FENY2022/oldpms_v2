<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $clientId = session()->get('client_id');
        $appModel = model('PermitApplicationModel');

        // Fetch stats
        $totalApps = $appModel->where('client_id', $clientId)->countAllResults();
        $pendingApps = $appModel->where('client_id', $clientId)->whereIn('status', ['Under Evaluation', 'Pending Review'])->countAllResults();
        $approvedApps = $appModel->where('client_id', $clientId)->whereIn('status', ['Approved', 'Issued', 'Completed'])->countAllResults();

        // Fetch notifications (Returned apps)
        $notifications = $appModel->where('client_id', $clientId)->where('status', 'Returned')->orderBy('date_submitted', 'DESC')->findAll();

        // Fetch requirements
        $requirements = model('RequirementModel')->orderBy('sequence', 'ASC')->findAll();

        // Fetch profile picture
        $userModel = model('UserClientModel');
        $user = $userModel->find($clientId);

        $data = [
            'totalApps'      => $totalApps,
            'pendingApps'    => $pendingApps,
            'approvedApps'   => $approvedApps,
            'notifications'  => $notifications,
            'notification_count' => count($notifications),
            'requirements'   => $requirements,
            'profilePicture' => $user['profile_picture'] ?? null,
        ];

        return view('client/dashboard', $data);
    }
}
