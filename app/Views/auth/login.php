<?= $this->extend('layouts/auth'); ?>

<?= $this->section('title'); ?>
Login
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="login-wrapper">
    <a href="/" class="btn-back-icon" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>Sistem Audit Internal</h1>
            <p>PT SanQua Multi Internasional</p>
        </div>

        <div class="login-body">
            <?php if(session()->getFlashdata('error')):?>
                <div class="alert alert-danger">
                    <strong>Error:</strong> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif;?>

            <form action="/auth/loginProcess" method="post">
                <div class="form-group">
                    <label for="inputEmail" class="form-label">📧 Email</label>
                    <input 
                        class="form-control" 
                        id="inputEmail" 
                        type="email" 
                        name="email" 
                        placeholder="nama@example.com" 
                        required 
                    />
                </div>

                <div class="form-group">
                    <label for="inputPassword" class="form-label">🔒 Password</label>
                    <input 
                        class="form-control" 
                        id="inputPassword" 
                        type="password" 
                        name="password" 
                        placeholder="Masukkan password Anda" 
                        required 
                    />
                </div>

                <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                    <button class="btn-login" type="submit">Masuk</button>
                </div>
            </form>
        </div>

        <div class="login-footer">
            <p class="footer-text">© 2024 <span class="footer-brand">PT SanQua</span> - Internal Audit System</p>
        </div>
    </div>
    </div>
</div>

<?= $this->endSection(); ?>