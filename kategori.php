<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

$page_title = 'Kategori';

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $error = 'Invalid CSRF token';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'add') {
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            
            if (empty($nama_kategori)) {
                $error = 'Nama kategori harus diisi';
            } else {
                $result = addKategori($conn, $nama_kategori);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        } elseif ($action === 'edit') {
            $id_kategori = $_POST['id_kategori'] ?? 0;
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            
            if (empty($nama_kategori)) {
                $error = 'Nama kategori harus diisi';
            } else {
                $result = updateKategori($conn, $id_kategori, $nama_kategori);
                if ($result['success']) {
                    $success = $result['message'];
                } else {
                    $error = $result['message'];
                }
            }
        } elseif ($action === 'delete') {
            $id_kategori = $_POST['id_kategori'] ?? 0;
            $result = deleteKategori($conn, $id_kategori);
            
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        }
    }
}

// Ambil semua kategori
$kategori_list = getAllKategori($conn);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Kategori</h2>
            <p class="text-muted">Kelola kategori barang</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
                <i class="bi bi-plus-circle"></i> Tambah Kategori
            </button>
        </div>
    </div>
    
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
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="80">No</th>
                                    <th>Nama Kategori</th>
                                    <th width="200">Tanggal Dibuat</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($kategori_list) > 0): ?>
                                    <?php foreach ($kategori_list as $index => $item): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editKategoriModal<?php echo $item['id_kategori']; ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteKategoriModal<?php echo $item['id_kategori']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editKategoriModal<?php echo $item['id_kategori']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Kategori</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="action" value="edit">
                                                            <input type="hidden" name="id_kategori" value="<?php echo $item['id_kategori']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label for="nama_kategori_edit_<?php echo $item['id_kategori']; ?>" class="form-label">Nama Kategori</label>
                                                                <input type="text" class="form-control" id="nama_kategori_edit_<?php echo $item['id_kategori']; ?>" name="nama_kategori" value="<?php echo htmlspecialchars($item['nama_kategori']); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Perbarui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteKategoriModal<?php echo $item['id_kategori']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Kategori</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id_kategori" value="<?php echo $item['id_kategori']; ?>">
                                                            
                                                            <p>Apakah Anda yakin ingin menghapus kategori <strong><?php echo htmlspecialchars($item['nama_kategori']); ?></strong>?</p>
                                                            <p class="text-danger"><small>Kategori yang sedang digunakan tidak dapat dihapus.</small></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data kategori</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Kategori Modal -->
<div class="modal fade" id="addKategoriModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
