<?php
// Fungsi untuk cek status login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

// Fungsi registrasi pengguna
function registerUser($conn, $nama, $username, $password) {
    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Username sudah digunakan'];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (nama, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nama, $username, $hashed_password);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Registrasi berhasil'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi validasi login
function loginUser($conn, $username, $password) {
    $stmt = $conn->prepare("SELECT id, nama, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            
            return ['success' => true, 'message' => 'Login berhasil'];
        }
    }
    
    return ['success' => false, 'message' => 'Username atau password salah'];
}

// Fungsi mengambil semua data barang
function getAllBarang($conn, $search = '', $kategori = '') {
    $sql = "SELECT b.*, k.nama_kategori 
            FROM barang b 
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (b.kode_barang LIKE ? OR b.nama_barang LIKE ? OR b.merek LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= "sss";
    }
    
    if (!empty($kategori)) {
        $sql .= " AND b.id_kategori = ?";
        $params[] = $kategori;
        $types .= "i";
    }
    
    $sql .= " ORDER BY b.created_at DESC";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi mengambil detail barang berdasarkan ID
function getBarangById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc();
}

// Fungsi menambah barang
function addBarang($conn, $data) {
    // Cek apakah kode barang sudah ada
    $stmt = $conn->prepare("SELECT id_barang FROM barang WHERE kode_barang = ?");
    $stmt->bind_param("s", $data['kode_barang']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Kode barang sudah digunakan'];
    }
    
    // Insert barang baru
    $stmt = $conn->prepare("INSERT INTO barang (kode_barang, nama_barang, id_kategori, merek, harga_beli, harga_jual, stok, satuan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisddis", 
        $data['kode_barang'], 
        $data['nama_barang'], 
        $data['id_kategori'], 
        $data['merek'], 
        $data['harga_beli'], 
        $data['harga_jual'], 
        $data['stok'], 
        $data['satuan']
    );
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Barang berhasil ditambahkan'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi update barang
function updateBarang($conn, $id, $data) {
    // Cek apakah kode barang sudah digunakan oleh barang lain
    $stmt = $conn->prepare("SELECT id_barang FROM barang WHERE kode_barang = ? AND id_barang != ?");
    $stmt->bind_param("si", $data['kode_barang'], $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Kode barang sudah digunakan'];
    }
    
    // Update barang
    $stmt = $conn->prepare("UPDATE barang SET kode_barang = ?, nama_barang = ?, id_kategori = ?, merek = ?, harga_beli = ?, harga_jual = ?, stok = ?, satuan = ? WHERE id_barang = ?");
    $stmt->bind_param("ssisddisi", 
        $data['kode_barang'], 
        $data['nama_barang'], 
        $data['id_kategori'], 
        $data['merek'], 
        $data['harga_beli'], 
        $data['harga_jual'], 
        $data['stok'], 
        $data['satuan'],
        $id
    );
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Barang berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi hapus barang
function deleteBarang($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Barang berhasil dihapus'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi mengambil semua kategori
function getAllKategori($conn) {
    $result = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Fungsi tambah kategori
function addKategori($conn, $nama_kategori) {
    // Cek apakah nama kategori sudah ada
    $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ?");
    $stmt->bind_param("s", $nama_kategori);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Nama kategori sudah ada'];
    }
    
    // Insert kategori baru
    $stmt = $conn->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt->bind_param("s", $nama_kategori);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Kategori berhasil ditambahkan'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi update kategori
function updateKategori($conn, $id, $nama_kategori) {
    // Cek apakah nama kategori sudah digunakan oleh kategori lain
    $stmt = $conn->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_kategori != ?");
    $stmt->bind_param("si", $nama_kategori, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ['success' => false, 'message' => 'Nama kategori sudah ada'];
    }
    
    // Update kategori
    $stmt = $conn->prepare("UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?");
    $stmt->bind_param("si", $nama_kategori, $id);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Kategori berhasil diperbarui'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi hapus kategori
function deleteKategori($conn, $id) {
    // Cek apakah kategori sedang digunakan
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM barang WHERE id_kategori = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['total'] > 0) {
        return ['success' => false, 'message' => 'Kategori tidak dapat dihapus karena sedang digunakan'];
    }
    
    // Hapus kategori
    $stmt = $conn->prepare("DELETE FROM kategori WHERE id_kategori = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Kategori berhasil dihapus'];
    } else {
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

// Fungsi hitung statistik dashboard
function getDashboardStats($conn) {
    $stats = [];
    
    // Total barang
    $result = $conn->query("SELECT COUNT(*) as total FROM barang");
    $stats['total_barang'] = $result->fetch_assoc()['total'];
    
    // Total kategori
    $result = $conn->query("SELECT COUNT(*) as total FROM kategori");
    $stats['total_kategori'] = $result->fetch_assoc()['total'];
    
    // Total stok
    $result = $conn->query("SELECT SUM(stok) as total FROM barang");
    $stats['total_stok'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Total nilai inventaris (harga jual * stok)
    $result = $conn->query("SELECT SUM(harga_jual * stok) as total FROM barang");
    $stats['nilai_inventaris'] = $result->fetch_assoc()['total'] ?? 0;
    
    return $stats;
}

// Fungsi format rupiah
function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Fungsi generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fungsi verifikasi CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
