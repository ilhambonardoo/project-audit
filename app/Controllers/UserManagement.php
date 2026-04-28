<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DepartmentModel;

class UserManagement extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->select('users.*, roles.role_name, departments.name as dept_name')
                ->join('roles', 'roles.id = users.role_id')
                ->join('departments', 'departments.id = users.department_id', 'left')
                ->findAll()
        ];

        return view('user_management/index', $data);
    }

    public function create()
    {
        $data = [
            'title'       => 'Tambah User',
            'roles'       => $this->roleModel->findAll(),
            'departments' => $this->departmentModel->findAll()
        ];

        return view('user_management/create', $data);
    }

    public function store()
    {
        $rules = [
            'name'          => 'required|min_length[3]',
            'email'         => 'required|valid_email|is_unique[users.email]',
            'password'      => 'required|min_length[6]',
            'role_id'       => 'required',
            'department_id' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dept = $this->departmentModel->find($this->request->getPost('department_id'));

        $this->userModel->save([
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'password'      => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role_id'       => $this->request->getPost('role_id'),
            'department_id' => $this->request->getPost('department_id'),
            'department'    => $dept['name'] ?? null
        ]);

        return redirect()->to('/user-management')->with('message', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'       => 'Edit User',
            'user'        => $user,
            'roles'       => $this->roleModel->findAll(),
            'departments' => $this->departmentModel->findAll()
        ];

        return view('user_management/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'name'          => 'required|min_length[3]',
            'email'         => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role_id'       => 'required',
            'department_id' => 'required'
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dept = $this->departmentModel->find($this->request->getPost('department_id'));

        $data = [
            'id'            => $id,
            'name'          => $this->request->getPost('name'),
            'email'         => $this->request->getPost('email'),
            'role_id'       => $this->request->getPost('role_id'),
            'department_id' => $this->request->getPost('department_id'),
            'department'    => $dept['name'] ?? null
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->save($data);

        return redirect()->to('/user-management')->with('message', 'User berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/user-management')->with('message', 'User berhasil dihapus.');
    }
}
