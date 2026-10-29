<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4><i class="bi bi-box-seam"></i> Manus Inventory</h4>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="barang.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' || basename($_SERVER['PHP_SELF']) == 'tambah_barang.php' || basename($_SERVER['PHP_SELF']) == 'edit_barang.php' ? 'active' : ''; ?>">
                <i class="bi bi-box"></i>
                <span>Data Barang</span>
            </a>
        </li>
        <li>
            <a href="kategori.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kategori.php' ? 'active' : ''; ?>">
                <i class="bi bi-tags"></i>
                <span>Kategori</span>
            </a>
        </li>
        <li>
            <a href="laporan.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'laporan.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan</span>
            </a>
        </li>
    </ul>
</div>
