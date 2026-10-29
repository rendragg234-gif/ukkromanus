<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

$page_title = 'Data Barang';

// Ambil parameter pencarian dan filter
$search = $_GET['search'] ?? '';
$kategori_filter = $_GET['kategori'] ?? '';

// Ambil data barang
$barang_list = getAllBarang($conn, $search, $kategori_filter);

// Ambil semua kategori untuk filter
$kategori_list = getAllKategori($conn);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Data Barang</h2>
            <p class="text-muted">Kelola data inventaris barang</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="tambah_barang.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </a>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Pencarian</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Cari kode, nama, atau merek barang..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="kategori" class="form-label">Kategori</label>
                            <select class="form-select" id="kategori" name="kategori">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategori_list as $kat): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>" <?php echo $kategori_filter == $kat['id_kategori'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Merek</th>
                                    <th>Harga Beli</th>
                                    <th>Harga Jual</th>
                                    <th>Stok</th>
                                    <th>Satuan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($barang_list) > 0): ?>
                                    <?php foreach ($barang_list as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['kode_barang']); ?></span></td>
                                            <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($item['merek']); ?></td>
                                            <td><?php echo formatRupiah($item['harga_beli']); ?></td>
                                            <td><?php echo formatRupiah($item['harga_jual']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $item['stok'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo number_format($item['stok']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                                            <td>
                                                <a href="edit_barang.php?id=<?php echo $item['id_barang']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="hapus_barang.php" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                                    <input type="hidden" name="id_barang" value="<?php echo $item['id_barang']; ?>">
                                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data barang</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <p class="text-muted mb-0">Total: <?php echo count($barang_list); ?> barang</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
