<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;
use App\Libraries\EmailService;
use App\Libraries\FileUploadService;

class MyApplications extends BaseController
{
    public function index()
    {
        $clientId = session()->get('client_id');
        $appModel = model('PermitApplicationModel');
        $logModel = model('ApplicationLogModel');
        $fileModel = model('PermitRequirementFileModel');
        $reqModel = model('RequirementModel');

        if ($this->request->is('post')) {
            $action = $this->request->getPost('action');

            if ($action === 'reupload_file') {
                $reqId = intval($this->request->getPost('requirement_id'));
                $appId = intval($this->request->getPost('app_id'));

                $app = $appModel->where('app_id', $appId)->first();

                if ($app && $app['client_id'] == $clientId) {
                    $dateSubmitted = strtotime($app['date_submitted']);
                    $timeElapsed = time() - $dateSubmitted;
                    $isReturned = ($app['status'] === 'Returned');

                    if ($timeElapsed <= 180 || $isReturned) {
                        $files = $this->request->getFile('new_files');
                        if ($files && is_array($files) && !empty($files[0]->getName())) {
                            $uploadService = new FileUploadService();
                            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];

                            // Delete old files
                            $oldFiles = $fileModel->where('app_id', $appId)->where('requirement_id', $reqId)->findAll();
                            foreach ($oldFiles as $old) {
                                $uploadService->deleteFile($old['file_path']);
                                $fileModel->delete($old['file_id']);
                            }

                            // Upload new files
                            foreach ($files as $i => $file) {
                                if ($file->isValid() && !$file->hasMoved()) {
                                    $ext = strtolower($file->getExtension());
                                    if (in_array($ext, $allowedExts)) {
                                        $dir = WRITEPATH . '../public/uploads/applications/';
                                        if (!is_dir($dir)) mkdir($dir, 0777, true);
                                        $newName = $appId . '_' . $reqId . '_' . $i . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                                        $file->move($dir, $newName);
                                        $fileModel->insert([
                                            'app_id'         => $appId,
                                            'requirement_id' => $reqId,
                                            'file_path'      => 'uploads/applications/' . $newName,
                                            'status'         => 'Pending',
                                        ]);
                                    }
                                }
                            }

                            $logModel->insert(['app_id' => $appId, 'action' => 'Document Updated', 'remarks' => 'Requirement document(s) were replaced/re-uploaded by the applicant.']);
                            return redirect()->to('/client/applications')->with('success', 'Document successfully updated!');
                        }
                    } else {
                        return redirect()->to('/client/applications')->with('error', 'Re-upload time limit has expired.');
                    }
                }
            } elseif ($action === 'resubmit_app') {
                $appId = intval($this->request->getPost('app_id'));
                $app = $appModel->where('app_id', $appId)->first();

                if ($app && $app['client_id'] == $clientId && $app['status'] === 'Returned') {
                    $appModel->update($appId, ['status' => 'Under Evaluation']);
                    $logModel->insert(['app_id' => $appId, 'action' => 'Application Resubmitted', 'remarks' => 'Applicant fixed incorrect documents and resubmitted the application for review.']);

                    // Send email to admin
                    $emailService = new EmailService();
                    $applicantName = session()->get('firstname') . ' ' . session()->get('lastname');
                    $formattedId = '#' . str_pad($appId, 5, '0', STR_PAD_LEFT);
                    $emailService->sendApplicationResubmittedEmail('venzonanthonie@gmail.com', $applicantName, $formattedId);

                    return redirect()->to('/client/applications')->with('success', 'Application successfully resubmitted for evaluation!');
                }
            }
        }

        // Fetch applications with timestamps
        $applications = $appModel->where('client_id', $clientId)->orderBy('app_id', 'DESC')->findAll();

        // Fetch logs and files for all applications
        $logs = [];
        $groupedFiles = [];

        if (!empty($applications)) {
            $appIds = array_column($applications, 'app_id');
            $allLogs = $logModel->whereIn('app_id', $appIds)->orderBy('created_at', 'DESC')->findAll();
            foreach ($allLogs as $log) {
                $logs[$log['app_id']][] = $log;
            }

            // Fetch files joined with requirements
            $db = \Config\Database::connect();
            $placeholders = implode(',', array_fill(0, count($appIds), '?'));
            $allFiles = $db->query("
                SELECT prf.*, r.requirement_name 
                FROM permit_requirements_files prf
                JOIN requirements r ON prf.requirement_id = r.id
                WHERE prf.app_id IN ($placeholders)
                ORDER BY r.sequence ASC, prf.file_id ASC
            ", $appIds)->getResultArray();

            foreach ($allFiles as $file) {
                $groupedFiles[$file['app_id']][$file['requirement_id']][] = $file;
            }
        }

        $data = [
            'applications'  => $applications,
            'logs'          => $logs,
            'groupedFiles'  => $groupedFiles,
            'requirements'  => $reqModel->orderBy('sequence', 'ASC')->findAll(),
        ];

        return view('client/my_applications', $data);
    }
}
