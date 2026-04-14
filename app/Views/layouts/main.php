<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Internal Audit System PT TMP BWN</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= base_url('css/main.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/navbar.css'); ?>">
    <?= $this->renderSection('styles'); ?>
  </head>
  <body>
    
    <?= $this->include('layouts/navbar') ?>

    <div class="main-content">
      <div class="container-fluid px-4">
        <?php if (session()->getFlashdata('success')) : ?>
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <strong>Sukses!</strong> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
          <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            <strong>Gagal!</strong> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('warning')) : ?>
          <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            <strong>Perhatian!</strong> <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
      </div>
    </div>

    <footer class="footer">
      <div class="container-fluid px-4">
        <div class="row">
          <div class="col-md-6">
            <p class="text-muted mb-0">&copy; 2026 <strong>PT TMP BWN</strong> - Internal Audit System. All rights reserved.</p>
          </div>
          <div class="col-md-6 text-end">
            <p class="text-muted mb-0">Version 1.0 | Last Updated: April 2026</p>
          </div>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
