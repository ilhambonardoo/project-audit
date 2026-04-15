<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="row align-items-center my-4">
    <div class="col">
        <h4 class="fw-bold mb-0">Daftar Temuan Audit</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2 mb-0">
                <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Temuan</li>
            </ol>
        </nav>
    </div>
    <div class="col-auto">
        <a href="/temuan/create" class="btn btn-primary rounded-pill px-4">
            <i class="bi bi-plus-lg me-2"></i> Tambah Temuan
        </a>
    </div>
</div>

<div class="card card-modern border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3 text-center" style="width: 50px;">No</th>
                        <th>Klausul</th>
                        <th>Judul Temuan</th>
                        <th>PIC</th>
                        <th class="text-center">Risiko</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Deadline</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($temuan)) : ?>
                        <?php $no = 1; ?>
                        <?php foreach ($temuan as $row) : ?>
                            <?php 
                                $deadline_date = strtotime($row['deadline']);
                                $today_date = strtotime(date('Y-m-d'));
                                $diff = ($deadline_date - $today_date) / (60 * 60 * 24);
                                
                                $risk_badge = 'bg-secondary';
                                if ($row['level_temuan'] == 'Tinggi') $risk_badge = 'bg-danger';
                                elseif ($row['level_temuan'] == 'Menengah') $risk_badge = 'bg-warning text-dark';
                                elseif ($row['level_temuan'] == 'Rendah') $risk_badge = 'bg-success';
                            ?>
                            <tr>
                                <td class="px-4 text-center"><?= $no++; ?></td>
                                <td class="fw-semibold text-primary"><?= $row['klausul']; ?></td>
                                <td><?= $row['judul_temuan']; ?></td>
                                <td><span class="badge border bg-light text-dark fw-normal"><i class="bi bi-person"></i> ID: <?= $row['pic_id']; ?></span></td>
                                <td class="text-center">
                                    <span class="badge <?= $risk_badge; ?> rounded-pill px-3">
                                        <?= $row['level_temuan']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-outline-primary border border-primary text-primary px-3 rounded-pill">
                                        <?= $row['status_progress']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($diff < 0) ? 'bg-danger' : 'bg-light text-dark'; ?> border">
                                        <?= date('d M Y', $deadline_date); ?>
                                        <?= ($diff < 0) ? ' (Overdue!)' : ''; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="/temuan/show/<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary rounded-start-pill px-3">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                        <a href="/temuan/edit/<?= $row['id'] ?>" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="/temuan/delete/<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger rounded-end-pill btn-hapus-temuan" data-id="<?= $row['id'] ?>" data-judul="<?= htmlspecialchars($row['judul_temuan']) ?>">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <img src="https://illustrations.popsy.co/blue/searching-for-answers.svg" alt="Empty" style="height: 150px;">
                                <p class="mt-3 text-muted">Belum ada data temuan audit yang tercatat.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusTemuan" tabindex="-1" aria-labelledby="modalHapusTemuanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalHapusTemuanLabel">Konfirmasi Hapus Temuan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus temuan berikut?</p>
        <div class="alert alert-danger mb-2">
          <strong id="judulTemuanHapus"></strong>
        </div>
        <small class="text-muted">Data yang dihapus <b>tidak bisa dikembalikan</b>.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="#" id="btnKonfirmasiHapus" class="btn btn-danger">Hapus</a>
      </div>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    let idHapus = null;
    document.querySelectorAll('.btn-hapus-temuan').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        idHapus = this.getAttribute('data-id');
        const judul = this.getAttribute('data-judul');
        document.getElementById('judulTemuanHapus').textContent = judul;
        document.getElementById('btnKonfirmasiHapus').setAttribute('href', '/temuan/delete/' + idHapus);
        const modal = new bootstrap.Modal(document.getElementById('modalHapusTemuan'));
        modal.show();
      });
    });
  });
</script>

<?= $this->endSection() ?>
