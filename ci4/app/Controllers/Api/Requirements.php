<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Requirements extends BaseController
{
    public function reorder()
    {
        $order = $this->request->getPost('order');
        if (is_array($order)) {
            $reqModel = model('RequirementModel');
            foreach ($order as $index => $id) {
                $reqModel->update($id, ['sequence' => $index + 1]);
            }
        }
        return $this->response->setJSON(['status' => 'success']);
    }
}
