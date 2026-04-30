<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;

class LoginController extends BaseController
{
    public function index()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $credential = $this->request->getJSON()->credential ?? null;

        if (!$credential) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Missing credential']);
        }

        // Verify Google ID token
        $client = \Config\Services::curlrequest();
        $response = $client->get('https://oauth2.googleapis.com/tokeninfo?id_token=' . $credential);

        if ($response->getStatusCode() !== 200) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid token']);
        }

        $payload = json_decode($response->getBody(), true);
        $email = $payload['email'] ?? null;

        if (!$email) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid token payload']);
        }

        // Whitelist check
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->where('is_active', true)->first();

        if (!$user) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Your email is not authorized']);
        }

        // Store google_sub on first login
        if (empty($user['google_sub'])) {
            $userModel->update($user['id'], ['google_sub' => $payload['sub']]);
        }

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
            'logged_in' => true,
        ]);

        return $this->response->setJSON([
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
            ],
        ]);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}