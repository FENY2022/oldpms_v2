<?php

namespace App\Controllers\Client;

use App\Controllers\BaseController;

class UpdateApplication extends BaseController
{
    public function index($appId = null)
    {
        $clientId = session()->get('client_id');
        $appModel = model('PermitApplicationModel');
        $app = $appModel->where('app_id', $appId)->where('client_id', $clientId)->first();

        if (!$app) {
            return redirect()->to(base_url('client/applications'))->with('error', 'Application not found.');
        }

        if ($this->request->is('post')) {
            try {
                $db = \Config\Database::connect();
                $db->transException(true)->start();

                $provinceId = htmlspecialchars($this->request->getPost('province_id'));
                $muncityId = htmlspecialchars($this->request->getPost('muncity_id'));
                $brgyId = htmlspecialchars($this->request->getPost('brgy_id'));
                $zipCode = htmlspecialchars($this->request->getPost('zip_code'));
                $streetAddress = htmlspecialchars($this->request->getPost('street_address'));

                $appModel->update($appId, [
                    'province_id'    => $provinceId,
                    'muncity_id'     => $muncityId,
                    'brgy_id'        => $brgyId,
                    'zip_code'       => $zipCode,
                    'street_address' => $streetAddress,
                ]);

                model('ApplicationLogModel')->insert([
                    'app_id'  => $appId,
                    'action'  => 'Application Updated',
                    'remarks' => 'Application details updated by the applicant.',
                ]);

                $db->completeTrans();
                return redirect()->to(base_url('client/applications'))->with('success', 'Application updated successfully!');
            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }
        }

        $data = [
            'app'         => $app,
            'provinces'   => model('ProvinceModel')->orderBy('prov_name', 'ASC')->findAll(),
            'muncities'   => model('MuncityModel')->orderBy('muncity_name', 'ASC')->findAll(),
            'barangays'   => model('BrgyModel')->orderBy('brgy_name', 'ASC')->findAll(),
        ];

        return view('client/update_application', $data);
    }
}
