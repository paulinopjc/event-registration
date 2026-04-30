<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class UserController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $users = $userModel->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users/index', ['users' => $users]);
    }

    public function create()
    {
        return view('admin/users/create');
    }

    public function store()
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'email' => 'required|valid_email|max_length[255]|is_unique[users.email]',
            'role' => 'required|in_list[admin,editor,viewer]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->save([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
            'is_active' => true,
        ]);

        return redirect()->to('/admin/users')->with('success', 'User created. They can now sign in with Google.');
    }

    public function edit(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        return view('admin/users/edit', ['user' => $user]);
    }

    public function update(int $id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        // Prevent self-demotion
        if ($id === (int) session()->get('user_id') && $this->request->getPost('role') !== 'admin') {
            return redirect()->back()->with('error', 'You cannot change your own role');
        }

        $rules = [
            'name' => 'required|max_length[255]',
            'role' => 'required|in_list[admin,editor,viewer]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel->update($id, [
            'name' => $this->request->getPost('name'),
            'role' => $this->request->getPost('role'),
        ]);

        return redirect()->to('/admin/users')->with('success', 'User updated');
    }

    public function deactivate(int $id)
    {
        // Prevent self-deactivation
        if ($id === (int) session()->get('user_id')) {
            return redirect()->back()->with('error', 'You cannot deactivate your own account');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found');
        }

        $userModel->update($id, ['is_active' => !$user['is_active']]);

        $action = $user['is_active'] ? 'deactivated' : 'activated';
        return redirect()->to('/admin/users')->with('success', "User {$action}");
    }
}
