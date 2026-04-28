<?php 
/** 
 * @var string $title
 */ 
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800"><?= $title; ?></h1>

            <?php if (session()->getFlashdata('message')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('message'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Antrean Persetujuan</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Klausul</th>
                                    <th>Judul Temuan</th>
                                    <th>Status Saat Ini</th>
                                    <th>Deadline</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($temuan)) : ?>
                                    <?php foreach ($temuan as $item) : ?>
                                        <tr>
                                            <td><?= $item['klausul']; ?></td>
                                            <td><?= $item['judul_temuan']; ?></td>
                                            <td><span class="badge bg-warning text-dark"><?= $item['status_progress']; ?></span></td>
                                            <td><?= $item['deadline']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm" 
                                                        onclick="openProcessModal(<?= $item['id']; ?>, '<?= $item['judul_temuan']; ?>')">
                                                    Proses
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada antrean persetujuan.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('approval/process'); ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="processModalLabel">Proses Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="temuan_id" id="modal_temuan_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Judul Temuan</label>
                        <input type="text" class="form-control" id="modal_judul_temuan" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keputusan (Approve/Reject)</label>
                        <select name="decision" class="form-select" required>
                            <option value="">-- Pilih Keputusan --</option>
                            <option value="Approve">Approve</option>
                            <option value="Reject">Reject</option>
                        </select>
                        <div class="form-text">Reject akan mengembalikan status ke 'On Progress'.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Tambahkan alasan atau catatan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Submit Persetujuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openProcessModal(id, judul) {
        document.getElementById('modal_temuan_id').value = id;
        document.getElementById('modal_judul_temuan').value = judul;
        var myModal = new bootstrap.Modal(document.getElementById('processModal'));
        myModal.show();
    }
</script>

<?= $this->endSection() ?>
