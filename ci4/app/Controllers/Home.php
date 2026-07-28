<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'requirements' => model('RequirementModel')->orderBy('sequence', 'ASC')->findAll(),
            'provinces' => model('ProvinceModel')->orderBy('prov_name', 'ASC')->findAll(),
        ];
        return view('home/index', $data);
    }
}
