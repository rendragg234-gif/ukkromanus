<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

$page_title = 'Dashboard';

// Ambil statistik
$stats = getDashboardStats($conn);

// Ambil 5 barang terbaru
$barang_terbaru = $conn->query("SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON b.id_kategori = k.id_kategori ORDER BY b.created_at DESC LIMIT 5");

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="mb-0">Dashboard</h2>
            <p class="text-muted">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card stat-card-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Barang</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_barang']); ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card stat-card-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Kategori</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_kategori']); ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-tags"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card stat-card-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Stok</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total_stok']); ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card stat-card stat-card-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Nilai Inventaris</h6>
                            <h3 class="mb-0" style="font-size: 1.3rem;"><?php echo formatRupiah($stats['nilai_inventaris']); ?></h3>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Items -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Barang Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Merek</th>
                                    <th>Harga Jual</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($barang_terbaru->num_rows > 0): ?>
                                    <?php while ($item = $barang_terbaru->fetch_assoc()): ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['kode_barang']); ?></span></td>
                                            <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($item['merek']); ?></td>
                                            <td><?php echo formatRupiah($item['harga_jual']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $item['stok'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo number_format($item['stok']); ?> <?php echo htmlspecialchars($item['satuan']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="edit_barang.php?id=<?php echo $item['id_barang']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Belum ada data barang</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <a href="barang.php" class="btn btn-primary">
                            <i class="bi bi-eye"></i> Lihat Semua Barang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
