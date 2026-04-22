<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Temuan Audit</h2>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="laporanTable">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Auditee</th>
                            <th>Departemen</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pic_groups as $index => $pic) : ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= esc($pic['pic_name']) ?></td>
                                <td><?= esc($pic['department']) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('laporan/preview/' . $pic['pic_id']) ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye"></i> Preview Gabungan
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
<?= $this->endSection() ?>
