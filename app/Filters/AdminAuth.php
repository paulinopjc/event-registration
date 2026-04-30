<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class AdminAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // Verify user is still active and refresh role from DB
        $userModel = new UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!$user || !$user['is_active']) {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Account deactivated');
        }

        // Keep session role in sync with DB
        if (session()->get('user_role') !== $user['role']) {
            session()->set('user_role', $user['role']);
        }

        // Check role-based access if arguments are provided
        if (!empty($arguments) && !in_array($user['role'], $arguments)) {
            return service('response')->setStatusCode(403, 'Forbidden')
                ->setBody(view('errors/html/error_403', ['message' => 'You do not have permission to access this page.']));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
