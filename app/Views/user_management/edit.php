<?php 
/** 
 * @var string $title
 * @var array{id: int|string, name: string, email: string, role_id: int|string, department_id: int|string} $user
 * @var array[] $roles
 * @var array[] $departments
 */ 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row pt-4">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow mb-4">
            <div class="card-header bg-info">
                <h5 class="card-title text-white mb-0"><?= $title ?></h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user-management/update/' . $user['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role / Peran</label>
                        <select class="form-control" id="role_id" name="role_id" required>
                            <option value="">-- Pilih Role --</option>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                    <?= $role['role_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="department_id" class="form-label">Penempatan Area (Auditee/Departemen)</label>
                        <select class="form-control" id="department_id" name="department_id" required>
                            <option value="">-- Pilih Departemen --</option>
                            <?php foreach ($departments as $dept) : ?>
                                <option value="<?= $dept['id'] ?>" <?= old('department_id', $user['department_id']) == $dept['id'] ? 'selected' : '' ?>>
                                    <?= $dept['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between pt-3 border-top">
                        <a href="<?= base_url('user-management') ?>" class="btn btn-secondary text-white">Kembali</a>
                        <button type="submit" class="btn btn-info text-white">Perbarui User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
