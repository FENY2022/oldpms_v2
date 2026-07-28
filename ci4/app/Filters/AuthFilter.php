<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/')->with('error', 'Please log in to access this page.');
        }

        if ($arguments !== null && !empty($arguments)) {
            $userType = $session->get('user_type');
            $requiredType = $arguments[0] ?? '';

            if ($requiredType === 'client' && $userType !== 'client') {
                return redirect()->to('/admin/dashboard');
            }
            if ($requiredType === 'admin' && $userType !== 'denr_user') {
                return redirect()->to('/client/dashboard');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
