<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Applications extends BaseController
{
    public function index()
    {
        $userId = session()->get('user_id');
        $userRoleId = (string)session()->get('user_role_id');
        $systemAdminRoles = ['15', '30', 'Admin'];
        $isSystemAdmin = in_array($userRoleId, $systemAdminRoles, true);

        $db = \Config\Database::connect();

        if ($isSystemAdmin) {
            $applications = $db->query("
                SELECT pa.*, uc.firstname, uc.lastname, m.muncity_name, m.office_cover 
                FROM permit_applications pa
                LEFT JOIN user_client uc ON pa.client_id = uc.client_id
                LEFT JOIN muncity m ON pa.muncity_id = m.mun_code
                ORDER BY pa.date_submitted DESC
            ")->getResultArray();
        } else {
            $denrUser = model('DenrUserModel')->find($userId);
            $officeId = $denrUser['office_id'] ?? 0;
            $applications = $db->query("
                SELECT pa.*, uc.firstname, uc.lastname, m.muncity_name, m.office_cover 
                FROM permit_applications pa
                LEFT JOIN user_client uc ON pa.client_id = uc.client_id
                JOIN muncity m ON pa.muncity_id = m.mun_code AND m.office_id = ?
                ORDER BY pa.date_submitted DESC
            ", [$officeId])->getResultArray();
        }

        return view('admin/applications', ['applications' => $applications]);
    }
}
