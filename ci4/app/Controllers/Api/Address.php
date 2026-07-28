<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Address extends BaseController
{
    public function getMunicipalities()
    {
        $provCode = $this->request->getPost('prov_code');
        $muncityModel = model('MuncityModel');
        $municipalities = $muncityModel->where('prov_code', $provCode)->orderBy('muncity_name', 'ASC')->select('mun_code, muncity_name, zip_code')->findAll();
        return $this->response->setJSON($municipalities);
    }

    public function getBarangays()
    {
        $munCode = $this->request->getPost('mun_code');
        $brgyModel = model('BrgyModel');
        $barangays = $brgyModel->where('mun_code', $munCode)->orderBy('brgy_name', 'ASC')->select('brgy_code, brgy_name')->findAll();
        return $this->response->setJSON($barangays);
    }
}
