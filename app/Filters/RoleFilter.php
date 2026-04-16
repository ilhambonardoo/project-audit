<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silahkan login terlebih dahulu!');
        }

        if (!empty($arguments)) {
            $userRole = session()->get('role_id');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
