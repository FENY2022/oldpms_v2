<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\EmailService;

class Contact extends BaseController
{
    public function submit()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $name = htmlspecialchars($this->request->getPost('name'));
        $email = htmlspecialchars($this->request->getPost('email'));
        $subject = htmlspecialchars($this->request->getPost('subject'));
        $message = htmlspecialchars($this->request->getPost('message'));

        if (empty($name) || empty($email) || empty($message)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please fill in all fields.']);
        }

        $contactModel = model('ContactMessageModel');
        if ($contactModel->insert(['name' => $name, 'email' => $email, 'subject' => $subject, 'message' => $message])) {
            $emailService = new EmailService();
            $emailService->sendContactAcknowledgement($email, $name, $subject);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Message sent and acknowledgement email delivered!']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to send message.']);
    }
}
