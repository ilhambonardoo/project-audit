<nav class="navbar navbar-expand-lg navbar-light navbar-modern sticky-top">
  <div class="container-fluid px-4">
    <a class="navbar-brand navbar-brand-modern" href="/">
      <i class="bi bi-shield-check"></i>
      <span class="brand-text">Audit System</span>
    </a>
    
    <button class="navbar-toggler navbar-toggler-modern" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link nav-item-modern <?= (url_is('/dashboard')) ? 'active' : '' ?>" href="/dashboard">
            <i class="bi bi-house-door"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <?php $role_id = session()->get('role_id'); ?>

        <?php if ($role_id == 1) : ?>
            <li class="nav-item">
              <a class="nav-link nav-item-modern <?= (url_is('temuan*')) ? 'active' : '' ?>" href="/temuan">
                <i class="bi bi-clipboard-check"></i>
                <span>Temuan</span>
              </a>
            </li>
        <?php endif; ?>

        <?php if ($role_id == 2) : ?>
            <li class="nav-item">
              <a class="nav-link nav-item-modern <?= (url_is('temuan*') || url_is('tindak-lanjut*')) ? 'active' : '' ?>" href="/temuan">
                <i class="bi bi-list-check"></i>
                <span>Temuan Saya</span>
              </a>
            </li>
        <?php endif; ?>

        <?php if (in_array($role_id, [3, 4, 5])) : ?>
            <li class="nav-item">
              <a class="nav-link nav-item-modern <?= url_is('/approval') ? "active" : "" ?>" href="/approval">
                <i class="bi bi-check-circle"></i>
                <span>Approval</span>
              </a>
            </li>
        <?php endif; ?>

        <?php if (in_array($role_id, [1, 3, 4, 5])) : ?>
            <li class="nav-item">
              <a class="nav-link nav-item-modern <?= url_is('/laporan') ? "active" : "" ?>" href="/laporan">
                <i class="bi bi-file-earmark-pdf"></i>
                <span>Laporan</span>
              </a>
            </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle nav-user" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle"></i>
            <span class="user-info">
              <small class="user-name"><?= session()->get('name'); ?></small>
              <small class="user-role"><?= session()->get('department'); ?></small>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end navbar-dropdown-modern">
            <li>
              <a class="dropdown-item" href="#">
                <i class="bi bi-person"></i> Profil Saya
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item text-danger" href="/logout">
                <i class="bi bi-box-arrow-right"></i> Logout
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>