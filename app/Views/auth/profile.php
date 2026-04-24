<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="row justify-content-center my-5">
    <div class="col-md-8">
        <div class="card shadow-lg border-0 rounded-4">
            <div class="card-header bg-dark text-white p-4 text-center rounded-top-4">
                <h4 class="mb-0 fw-bold">Profil Pengguna</h4>
            </div>
            <div class="card-body p-5">
                <?php if (session()->getFlashdata('message')) : ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('message') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Nama</div>
                    <div class="col-sm-8"><?= esc($user['name']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Email</div>
                    <div class="col-sm-8"><?= esc($user['email']) ?></div>
                </div>
                <div class="row mb-4">
                    <div class="col-sm-4 text-muted fw-bold">Departemen</div>
                    <div class="col-sm-8"><?= esc($user['department']) ?></div>
                </div>

                <hr class="my-5">

                <h5 class="fw-bold mb-4 text-primary"><i class="bi bi-pencil-square me-2"></i>Tanda Tangan Digital</h5>
                
                <div class="text-center mb-4">
                    <div class="p-4 border rounded-4 bg-light shadow-inner" style="min-height: 200px;">
                        <?php if ($user['signature']) : ?>
                            <img src="<?= $user['signature'] ?>" alt="Tanda Tangan" class="img-fluid border p-2 bg-white rounded shadow-sm" style="max-height: 180px;">
                        <?php else : ?>
                            <div class="py-5 text-muted">
                                <i class="bi bi-vector-pen display-4 d-block mb-3 opacity-25"></i>
                                <p>Belum ada tanda tangan digital</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="text-center">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-5 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#signatureModal">
                        <i class="bi bi-pen me-2"></i> Update Tanda Tangan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Signature -->
<div class="modal fade" id="signatureModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 overflow-hidden border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3 border-0">
                <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Buat Tanda Tangan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/profile/update" method="POST">
                <div class="modal-body p-4 text-center">
                    <p class="text-muted small mb-4">Silakan gambar tanda tangan Anda pada area di bawah ini:</p>
                    
                    <div class="position-relative mb-5">
                        <div class="canvas-container border-dashed rounded-pill bg-light mx-auto position-relative" style="cursor: crosshair; width: 350px; height: 100px; border: 1px dashed #ccc !important;">
                            <canvas id="signature-pad" style="width: 100%; height: 100%; touch-action: none;"></canvas>
                        </div>
                        <button type="button" id="clear" class="btn btn-sm btn-white border rounded-pill px-3 position-absolute shadow-sm" style="bottom: -15px; left: 50%; transform: translateX(-50%); font-size: 0.7rem; background: #fff;">
                            <i class="bi bi-eraser-fill text-muted me-1"></i> Bersihkan
                        </button>
                    </div>
                    
                    <input type="hidden" name="signature" id="signature-data">
                    
                    <div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal" style="background-color: #6c757d; border: none; min-width: 100px;">Batal</button>
                        <button type="submit" id="save" class="btn btn-primary rounded-pill px-4 fw-bold" style="background-color: #007bff; border: none; min-width: 180px;">Simpan Tanda Tangan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .modal-content { border-radius: 1.2rem !important; }
    .bg-primary { background-color: #007bff !important; }
    .border-dashed { border: 1px dashed #d1d1d1 !important; }
    .canvas-container { box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
    .btn-white:hover { background-color: #f8f9fa; }
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const canvas = document.getElementById('signature-pad');
        const signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const container = canvas.parentElement;
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        // Handle window resize and modal show
        window.onresize = resizeCanvas;
        document.getElementById('signatureModal').addEventListener('shown.bs.modal', function () {
            resizeCanvas();
        });

        document.getElementById('clear').addEventListener('click', function() {
            signaturePad.clear();
        });

        document.getElementById('save').addEventListener('click', function(e) {
            if (signaturePad.isEmpty()) {
                alert("Silakan buat tanda tangan terlebih dahulu.");
                e.preventDefault();
            } else {
                const data = signaturePad.toDataURL('image/png');
                document.getElementById('signature-data').value = data;
            }
        });
    });
</script>
<?= $this->section('scripts') ?>
<?php // SignaturePad is handled directly in script tag above for simplicity ?>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
