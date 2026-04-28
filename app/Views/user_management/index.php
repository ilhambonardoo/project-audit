<?php 
/** 
 * @var string $title
 * @var array<int, array{id: int|string, name: string, email: string, role_name: string, dept_name: string|null, department: string|null}> $users
 */ 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row pt-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            <a href="<?= base_url('user-management/create') ?>" class="btn btn-primary d-flex align-items-center">
                <i class="bi bi-person-plus me-2"></i> Tambah User Baru
            </a>
        </div>

        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-success mt-2">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="userTable" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Departemen / PIC</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $user['name'] ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= $user['role_name'] ?></span>
                                    </td>
                                    <td><?= $user['dept_name'] ?: ($user['department'] ?: '-') ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('user-management/edit/' . $user['id']) ?>" class="btn btn-sm btn-info" title="Edit">
                                            <i class="bi bi-pencil text-white"></i>
                                        </a>
                                        <a href="<?= base_url('user-management/delete/' . $user['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
