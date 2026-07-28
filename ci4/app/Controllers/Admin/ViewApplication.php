<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\EmailService;

class ViewApplication extends BaseController
{
    public function index($appId = null)
    {
        if (!$appId) {
            return redirect()->to(base_url('admin/applications'))->with('error', 'Invalid Application ID.');
        }

        $userModel = model('UserClientModel');
        $appModel = model('PermitApplicationModel');
        $fileModel = model('PermitRequirementFileModel');
        $logModel = model('ApplicationLogModel');
        $reqModel = model('RequirementModel');

        // Handle AJAX file status update
        if ($this->request->is('post') && $this->request->getPost('ajax_update_file')) {
            $this->response->setHeader('Content-Type', 'application/json');
            $fileId = intval($this->request->getPost('file_id'));
            $newStatus = $this->request->getPost('status');
            $remarks = $this->request->getPost('remarks') ? trim($this->request->getPost('remarks')) : null;

            $fileModel->update($fileId, ['status' => $newStatus, 'remarks' => $remarks]);
            return $this->response->setJSON(['success' => true, 'status' => $newStatus, 'remarks' => $remarks]);
        }

        // Handle status update
        if ($this->request->is('post') && $this->request->getPost('update_application')) {
            $newStatus = $this->request->getPost('status');
            $remarks = trim($this->request->getPost('remarks'));
            $userName = session()->get('name') ?? 'System User';

            try {
                $db = \Config\Database::connect();
                $db->transException(true)->start();

                $appModel->update($appId, ['status' => $newStatus]);

                $logModel->insert([
                    'app_id'   => $appId,
                    'action'   => "Status updated to: $newStatus",
                    'remarks'  => "Evaluated by $userName - $remarks",
                ]);

                $db->completeTrans();

                if ($newStatus === 'Returned') {
                    $app = $appModel->find($appId);
                    if ($app) {
                        $client = $userModel->find($app['client_id']);
                        if ($client && !empty($client['email'])) {
                            $emailService = new EmailService();
                            $formattedId = '#' . str_pad($appId, 5, '0', STR_PAD_LEFT);
                            $emailService->sendApplicationReturnedEmail($client['email'], $client['firstname'], $formattedId, $remarks);
                        }
                    }
                    return redirect()->to("/admin/view-application/$appId")->with('success', "Application returned and client notified.");
                }

                return redirect()->to("/admin/view-application/$appId")->with('success', 'Application status updated!');
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->to("/admin/view-application/$appId")->with('error', 'Failed to update: ' . $e->getMessage());
            }
        }

        // Fetch application details
        $db = \Config\Database::connect();
        $application = $db->query("
            SELECT pa.*, uc.firstname, uc.lastname, uc.email as client_email, uc.mobilenum
            FROM permit_applications pa
            LEFT JOIN user_client uc ON pa.client_id = uc.client_id
            WHERE pa.app_id = ?
        ", [$appId])->getRowArray();

        if (!$application) {
            return redirect()->to(base_url('admin/applications'))->with('error', 'Application not found.');
        }

        // Fetch files grouped by requirement
        $allFiles = $db->query("
            SELECT prf.*, r.requirement_name 
            FROM permit_requirements_files prf
            JOIN requirements r ON prf.requirement_id = r.id
            WHERE prf.app_id = ?
            ORDER BY r.sequence ASC, prf.file_id ASC
        ", [$appId])->getResultArray();

        $groupedFiles = [];
        foreach ($allFiles as $file) {
            $groupedFiles[$file['requirement_id']][] = $file;
        }

        // Fetch logs
        $logs = $logModel->where('app_id', $appId)->orderBy('created_at', 'DESC')->findAll();

        // Fetch requirements
        $requirements = $reqModel->orderBy('sequence', 'ASC')->findAll();

        $data = [
            'application'  => $application,
            'groupedFiles' => $groupedFiles,
            'logs'         => $logs,
            'requirements' => $requirements,
        ];

        return view('admin/view_application', $data);
    }
}
