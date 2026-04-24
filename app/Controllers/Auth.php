<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;

class Auth extends BaseController
{
    protected $user_model;
    
    public function __construct() {
        $this->user_model = new UserModel();
    }

    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function loginProcess()
    {
        $session = session();
        $userModel = $this->user_model;
        
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        
        $user = $userModel->where('email', $email)->first();
        
        if ($user) {
            if (password_verify($password, $user['password'])) {
                $ses_data = [
                    'id'         => $user['id'],
                    'name'       => $user['name'],
                    'email'      => $user['email'],
                    'role_id'    => $user['role_id'],
                    'department' => $user['department'],
                    'signature'  => $user['signature'],
                    'isLoggedIn' => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('error', 'Email atau Password salah!');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Akun tidak ditemukan!');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function profile()
    {
        $user = $this->user_model->find(session()->get('id'));
        $data = [
            'title' => 'Profil Saya',
            'user'  => $user
        ];
        return view('auth/profile', $data);
    }

    public function updateProfile()
    {
        $userId = session()->get('id');
        $signature = $this->request->getPost('signature');

        if ($signature) {
            $this->user_model->update($userId, [
                'signature' => $signature
            ]);
            session()->set('signature', $signature);
            return redirect()->to('/profile')->with('message', 'Tanda tangan berhasil diperbarui.');
        }

        return redirect()->to('/profile')->with('error', 'Gagal memperbarui tanda tangan.');
    }
}