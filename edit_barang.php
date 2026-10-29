<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

$page_title = 'Edit Barang';

$error = '';
$success = '';
$id = $_GET['id'] ?? 0;

// Ambil data barang
$barang = getBarangById($conn, $id);

if (!$barang) {
    header("Location: barang.php");
    exit();
}

// Ambil semua kategori
$kategori_list = getAllKategori($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Invalid CSRF token';
    } else {
        $kode_barang = trim($_POST['kode_barang'] ?? '');
        $nama_barang = trim($_POST['nama_barang'] ?? '');
        $id_kategori = $_POST['id_kategori'] ?? '';
        $merek = trim($_POST['merek'] ?? '');
        $harga_beli = $_POST['harga_beli'] ?? 0;
        $harga_jual = $_POST['harga_jual'] ?? 0;
        $stok = $_POST['stok'] ?? 0;
        $satuan = trim($_POST['satuan'] ?? 'pcs');
        
        // Validasi input
        if (empty($kode_barang) || empty($nama_barang) || empty($id_kategori)) {
            $error = 'Kode barang, nama barang, dan kategori harus diisi';
        } elseif ($harga_beli < 0 || $harga_jual < 0) {
            $error = 'Harga tidak boleh negatif';
        } elseif ($stok < 0) {
            $error = 'Stok tidak boleh negatif';
        } else {
            $data = [
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'id_kategori' => $id_kategori,
                'merek' => $merek,
                'harga_beli' => $harga_beli,
                'harga_jual' => $harga_jual,
                'stok' => $stok,
                'satuan' => $satuan
            ];
            
            $result = updateBarang($conn, $id, $data);
            
            if ($result['success']) {
                $success = $result['message'];
                // Refresh data barang
                $barang = getBarangById($conn, $id);
            } else {
                $error = $result['message'];
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Edit Barang</h2>
            <p class="text-muted">Perbarui data barang</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="barang.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kode_barang" class="form-label">Kode Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kode_barang" name="kode_barang" value="<?php echo htmlspecialchars($barang['kode_barang']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="nama_barang" class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-select" id="id_kategori" name="id_kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php foreach ($kategori_list as $kat): ?>
                                        <option value="<?php echo $kat['id_kategori']; ?>" <?php echo $barang['id_kategori'] == $kat['id_kategori'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="merek" class="form-label">Merek</label>
                                <input type="text" class="form-control" id="merek" name="merek" value="<?php echo htmlspecialchars($barang['merek']); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="harga_beli" class="form-label">Harga Beli (Rp)</label>
                                <input type="number" class="form-control" id="harga_beli" name="harga_beli" value="<?php echo $barang['harga_beli']; ?>" min="0" step="0.01">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="harga_jual" class="form-label">Harga Jual (Rp)</label>
                                <input type="number" class="form-control" id="harga_jual" name="harga_jual" value="<?php echo $barang['harga_jual']; ?>" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $barang['stok']; ?>" min="0">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select" id="satuan" name="satuan">
                                    <option value="pcs" <?php echo $barang['satuan'] == 'pcs' ? 'selected' : ''; ?>>Pcs</option>
                                    <option value="unit" <?php echo $barang['satuan'] == 'unit' ? 'selected' : ''; ?>>Unit</option>
                                    <option value="box" <?php echo $barang['satuan'] == 'box' ? 'selected' : ''; ?>>Box</option>
                                    <option value="set" <?php echo $barang['satuan'] == 'set' ? 'selected' : ''; ?>>Set</option>
                                    <option value="pack" <?php echo $barang['satuan'] == 'pack' ? 'selected' : ''; ?>>Pack</option>
                                    <option value="lusin" <?php echo $barang['satuan'] == 'lusin' ? 'selected' : ''; ?>>Lusin</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="barang.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Perbarui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
