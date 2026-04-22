<?= $this->extend('layouts/auth'); ?>

<?= $this->section('title'); ?>
Login
<?= $this->endSection(); ?>

<?= $this->section('content'); ?>

<div class="login-wrapper">
    <a href="/" class="btn-back-icon" title="Kembali" style="position: absolute; top: 20px; left: 20px; color: var(--primary); font-size: 24px;">
        <i class="bi bi-arrow-left text-light"></i>
    </a>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="header-logo">
                    <i class="bi bi-shield-check" style="font-size: 40px; color: white;"></i>
                </div>
                <h1>Sistem Audit Internal</h1>
                <p>PT SanQua Multi Internasional</p>
            </div>

            <div class="login-body">
                <?php if(session()->getFlashdata('error')):?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif;?>

                <form action="/auth/loginProcess" method="post">
                    <div class="form-group mb-4">
                        <label for="inputEmail" class="form-label fw-bold small text-muted text-uppercase mb-2">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                            <input 
                                class="form-control bg-light border-start-0" 
                                id="inputEmail" 
                                type="email" 
                                name="email" 
                                placeholder="nama@example.com" 
                                required 
                            />
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="inputPassword" class="form-label fw-bold small text-muted text-uppercase mb-2">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-primary"></i></span>
                            <input 
                                class="form-control bg-light border-start-0" 
                                id="inputPassword" 
                                type="password" 
                                name="password" 
                                placeholder="Masukkan password Anda" 
                                required 
                            />
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr; gap: 12px;">
                        <button class="btn-login" type="submit" style="background: var(--primary); border: none; padding: 12px; border-radius: 8px; color: white; font-weight: 600; transition: all 0.3s ease;">
                            Masuk Ke Sistem
                        </button>
                    </div>
                </form>
            </div>

            <div class="login-footer py-3 text-center border-top">
                <p class="footer-text mb-0 small text-muted">© 2024 <span class="footer-brand fw-bold text-primary">PT SanQua</span></p>
                <span class="small text-muted">Internal Audit System v1.0</span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>