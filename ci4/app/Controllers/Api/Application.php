<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Application extends BaseController
{
    public function updateFileStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');
        $fileId = intval($this->request->getPost('file_id'));
        $newStatus = $this->request->getPost('status');
        $remarks = $this->request->getPost('remarks') ? trim($this->request->getPost('remarks')) : null;

        try {
            $fileModel = model('PermitRequirementFileModel');
            $fileModel->update($fileId, ['status' => $newStatus, 'remarks' => $remarks]);
            return $this->response->setJSON(['success' => true, 'status' => $newStatus, 'remarks' => $remarks]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
