<div class="topbar">
    <div class="page-title">
        🏢 Sistema Tower
    </div>

    <div class="top-icons">
        <a href="#"><i class="fas fa-comment"></i></a>

        <a href="<?php echo $url_base . 'alertas/'; ?>" class="notification">
            <i class="fas fa-bell"></i>

            <?php if (!empty($totalAlertas) && $totalAlertas > 0): ?>
                <span class="badge"><?php echo $totalAlertas; ?></span>
            <?php endif; ?>
        </a>

        <div class="dropdown">
            <a href="#" class="text-white dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-user"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li class="dropdown-item text-muted small">
                    <?php echo $_SESSION['usuario']; ?>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger fw-semibold"
                       href="<?php echo $url_base; ?>cerrar.php">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
