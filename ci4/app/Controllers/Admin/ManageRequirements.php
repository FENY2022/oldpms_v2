<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ManageRequirements extends BaseController
{
    public function index()
    {
        $reqModel = model('RequirementModel');

        if ($this->request->is('post')) {
            $action = $this->request->getPost('action');

            if ($action === 'add') {
                $name = $this->request->getPost('requirement_name');
                $newStatus = $this->request->getPost('new_app_status');
                $renewStatus = $this->request->getPost('renewal_app_status');

                $downloadLink = null;
                $file = $this->request->getFile('file_upload');
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $dir = WRITEPATH . '../public/documents/';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $newName = time() . '_' . $file->getRandomName();
                    $file->move($dir, $newName);
                    $downloadLink = 'documents/' . $newName;
                }

                $nextSeq = $reqModel->countAllResults() > 0 ? ($reqModel->selectMax('sequence')->get()->getRow()->sequence + 1) : 1;

                $reqModel->insert([
                    'requirement_name'   => $name,
                    'new_app_status'     => $newStatus,
                    'renewal_app_status' => $renewStatus,
                    'download_link'      => $downloadLink,
                    'sequence'           => $nextSeq,
                ]);

                return redirect()->back()->with('success', 'Requirement added successfully!');
            }

            if ($action === 'edit') {
                $id = $this->request->getPost('id');
                $name = $this->request->getPost('requirement_name');
                $newStatus = $this->request->getPost('new_app_status');
                $renewStatus = $this->request->getPost('renewal_app_status');
                $removeFile = $this->request->getPost('remove_file') ? true : false;

                $oldReq = $reqModel->find($id);
                $oldFile = $oldReq['download_link'] ?? null;

                $file = $this->request->getFile('file_upload');
                $newFilePath = null;
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $dir = WRITEPATH . '../public/documents/';
                    if (!is_dir($dir)) mkdir($dir, 0777, true);
                    $newName = time() . '_' . $file->getRandomName();
                    $file->move($dir, $newName);
                    $newFilePath = 'documents/' . $newName;
                }

                $updateData = [
                    'requirement_name'   => $name,
                    'new_app_status'     => $newStatus,
                    'renewal_app_status' => $renewStatus,
                ];

                if ($newFilePath) {
                    if ($oldFile && file_exists(WRITEPATH . '../public/' . $oldFile)) unlink(WRITEPATH . '../public/' . $oldFile);
                    $updateData['download_link'] = $newFilePath;
                } elseif ($removeFile && $oldFile) {
                    if (file_exists(WRITEPATH . '../public/' . $oldFile)) unlink(WRITEPATH . '../public/' . $oldFile);
                    $updateData['download_link'] = null;
                }

                $reqModel->update($id, $updateData);
                return redirect()->back()->with('success', 'Requirement updated successfully!');
            }
        }

        if ($this->request->getGet('delete_id')) {
            $id = $this->request->getGet('delete_id');
            $req = $reqModel->find($id);
            if ($req && $req['download_link'] && file_exists(WRITEPATH . '../public/' . $req['download_link'])) {
                unlink(WRITEPATH . '../public/' . $req['download_link']);
            }
            $reqModel->delete($id);
            return redirect()->back()->with('success', 'Requirement deleted successfully!');
        }

        $requirements = $reqModel->orderBy('sequence', 'ASC')->orderBy('id', 'ASC')->findAll();

        return view('admin/manage_requirements', ['requirements' => $requirements]);
    }

    public function delete($id = null)
    {
        if ($id) {
            $reqModel = model('RequirementModel');
            $req = $reqModel->find($id);
            if ($req && $req['download_link'] && file_exists(WRITEPATH . '../public/' . $req['download_link'])) {
                unlink(WRITEPATH . '../public/' . $req['download_link']);
            }
            $reqModel->delete($id);
            return redirect()->back()->with('success', 'Requirement deleted successfully!');
        }
        return redirect()->to('/admin/manage-requirements');
    }
}
