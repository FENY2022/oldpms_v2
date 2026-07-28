<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;

class CreateApplication extends BaseController
{
    public function index()
    {
        $clientId = session()->get('client_id');
        $appType = $this->request->getGet('type') ?? 'New';
        $appType = ucfirst($appType);

        $userModel = model('UserClientModel');
        $user = $userModel->find($clientId);

        if (!$user) {
            return redirect()->to(base_url('client/dashboard'))->with('error', 'User not found.');
        }

        if ($this->request->is('post')) {
            try {
                $db = \Config\Database::connect();
                $db->transException(true)->start();

                $applicantType = htmlspecialchars($this->request->getPost('applicant_type'));
                $businessName = htmlspecialchars($this->request->getPost('business_name'));
                $tinNumber = htmlspecialchars($this->request->getPost('tin_number'));
                $provinceId = htmlspecialchars($this->request->getPost('province_id'));
                $muncityId = htmlspecialchars($this->request->getPost('muncity_id'));
                $brgyId = htmlspecialchars($this->request->getPost('brgy_id'));
                $zipCode = htmlspecialchars($this->request->getPost('zip_code'));
                $streetAddress = htmlspecialchars($this->request->getPost('street_address'));
                $referenceNumber = $this->request->getPost('reference_number') ? htmlspecialchars($this->request->getPost('reference_number')) : null;

                $appModel = model('PermitApplicationModel');
                $newAppId = $appModel->insert([
                    'client_id'         => $clientId,
                    'app_type'          => $appType,
                    'applicant_type'    => $applicantType,
                    'business_name'     => $businessName,
                    'tin_number'        => $tinNumber,
                    'reference_number'  => $referenceNumber,
                    'province_id'       => $provinceId,
                    'muncity_id'        => $muncityId,
                    'brgy_id'           => $brgyId,
                    'zip_code'          => $zipCode,
                    'street_address'    => $streetAddress,
                ]);

                // Handle file uploads
                $requirements = model('RequirementModel')->orderBy('sequence', 'ASC')->findAll();
                $fileModel = model('PermitRequirementFileModel');
                $dir = WRITEPATH . '../public/uploads/applications/';
                if (!is_dir($dir)) mkdir($dir, 0777, true);

                foreach ($requirements as $req) {
                    $inputName = 'req_' . $req['id'];
                    $isOptionalNew = ($appType === 'New' && $req['id'] == 6);
                    $userOptedIn = $this->request->getPost('include_req_6') === 'yes';

                    if ($isOptionalNew && !$userOptedIn) continue;

                    $files = $this->request->getFiles();
                    if (!empty($files[$inputName])) {
                        $uploadedFiles = $files[$inputName];
                        if (!is_array($uploadedFiles)) $uploadedFiles = [$uploadedFiles];

                        foreach ($uploadedFiles as $i => $file) {
                            if ($file->isValid() && !$file->hasMoved()) {
                                $ext = strtolower($file->getExtension());
                                if ($ext !== 'pdf') {
                                    throw new \Exception("Invalid file type for '" . $req['requirement_name'] . "'. Only PDF files are allowed.");
                                }
                                $newName = $newAppId . '_' . $req['id'] . '_' . $i . '_' . time() . '.' . $ext;
                                $file->move($dir, $newName);
                                $fileModel->insert([
                                    'app_id'         => $newAppId,
                                    'requirement_id' => $req['id'],
                                    'file_path'      => 'uploads/applications/' . $newName,
                                ]);
                            }
                        }
                    } else {
                        throw new \Exception("Missing required document: " . $req['requirement_name']);
                    }
                }

                // Log submission
                model('ApplicationLogModel')->insert([
                    'app_id'  => $newAppId,
                    'action'  => 'Application Submitted',
                    'remarks' => 'Application successfully submitted subject for evaluation.',
                ]);

                $db->completeTrans();
                return redirect()->to(base_url('client/applications'))->with('success', 'Application submitted successfully!');
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        $data = [
            'user'        => $user,
            'appType'     => $appType,
            'provinces'   => model('ProvinceModel')->orderBy('prov_name', 'ASC')->findAll(),
            'muncities'   => model('MuncityModel')->orderBy('muncity_name', 'ASC')->findAll(),
            'barangays'   => model('BrgyModel')->orderBy('brgy_name', 'ASC')->findAll(),
            'requirements' => model('RequirementModel')->orderBy('sequence', 'ASC')->findAll(),
        ];

        return view('client/create_application', $data);
    }
}
