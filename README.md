# Manus Inventory System

Aplikasi Manajemen Inventaris Barang berbasis PHP Native dan MySQL untuk Sertifikasi Junior Web Developer (JWD).

## Deskripsi

Manus Inventory adalah aplikasi web CRUD (Create, Read, Update, Delete) untuk mengelola data inventaris barang. Aplikasi ini dibangun menggunakan teknologi web modern dengan antarmuka yang responsif dan user-friendly.

## Fitur Utama

- ✅ **Autentikasi Pengguna** - Login dan Registrasi dengan password terenkripsi
- ✅ **Dashboard** - Menampilkan statistik dan ringkasan inventaris
- ✅ **Manajemen Barang** - CRUD data barang lengkap dengan pencarian dan filter
- ✅ **Manajemen Kategori** - Kelola kategori barang
- ✅ **Laporan PDF** - Generate laporan inventaris dalam format PDF
- ✅ **Responsive Design** - Tampilan optimal di desktop, tablet, dan mobile
- ✅ **Keamanan** - Prepared statements, CSRF protection, password hashing

## Teknologi yang Digunakan

- **Backend:** PHP Native (tanpa framework)
- **Database:** MySQL via phpMyAdmin
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **PDF Generator:** Dompdf 3.1.6 (paket manual, tanpa Composer)
- **Server:** XAMPP (Apache + MySQL)

## Persyaratan Sistem

- XAMPP versi 7.4 atau lebih tinggi
- PHP 7.4+
- MySQL 5.7+ atau MariaDB 10.4+
- Web Browser modern (Chrome, Firefox, Edge, Safari)

> **Tidak perlu Composer** — library Dompdf sudah disertakan lengkap di folder `vendor/dompdf`.

## Struktur Folder

```
manus_inventory/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── config/
│   └── koneksi.php
├── includes/
│   ├── auth.php
│   ├── functions.php
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── vendor/
│   └── dompdf/           (paket Dompdf manual, ikut ter-commit ke GitHub)
├── barang.php
├── dashboard.php
├── database.sql
├── edit_barang.php
├── hapus_barang.php
├── index.php
├── kategori.php
├── laporan.php
├── login.php
├── logout.php
├── register.php
├── tambah_barang.php
├── composer.json
└── README.md
```

## Instalasi

### Langkah 1: Persiapan XAMPP

1. Download dan install XAMPP dari [https://www.apachefriends.org](https://www.apachefriends.org)
2. Jalankan XAMPP Control Panel
3. Start modul **Apache** dan **MySQL**

### Langkah 2: Setup Project

1. Copy folder `manus_inventory` ke direktori:
   ```
   C:\xampp\htdocs\manus_inventory
   ```

### Langkah 3: Setup Database

1. Buka web browser dan akses [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Klik tab **"Import"**
3. Klik tombol **"Choose File"** dan pilih file `database.sql`
4. Klik tombol **"Go"** untuk mengimport database
5. Database `db_manus_inventory` akan otomatis terbuat beserta tabelnya

### Langkah 4: Konfigurasi Database (Opsional)

Jika menggunakan username/password MySQL yang berbeda, edit file `config/koneksi.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Sesuaikan username MySQL
define('DB_PASS', '');            // Sesuaikan password MySQL
define('DB_NAME', 'db_manus_inventory');
```

### Langkah 5: Dompdf (Sudah Terpasang — Tanpa Composer)

Aplikasi ini **tidak memerlukan Composer**. Library Dompdf sudah disertakan
secara lengkap di dalam project pada folder `vendor/dompdf` (paket resmi
Dompdf 3.1.6 dari GitHub releases beserta seluruh dependencies-nya).

Cukup verifikasi file berikut ada setelah clone/copy project:

```
manus_inventory/
├── vendor/
│   └── dompdf/
│       ├── autoload.inc.php        <- WAJIB ADA
│       └── vendor/
│           └── autoload.php        <- WAJIB ADA
└── ...
```

Jika folder tersebut hilang (misalnya setelah re-download), silakan:

1. Download paket `dompdf-3.1.6.zip` (bukan "Source code" ZIP) dari
   [https://github.com/dompdf/dompdf/releases](https://github.com/dompdf/dompdf/releases)
2. Extract dan copy folder hasil extract ke: `manus_inventory\vendor\dompdf`

Detail lengkap ada di file `INSTALL_DOMPDF.md`.

**Catatan Penting:** 
- Fitur cetak PDF memerlukan Dompdf. Tanpa ini, hanya tombol Cetak PDF yang menampilkan petunjuk pemasangan.
- Semua fitur lainnya (login, CRUD barang, kategori) tetap berfungsi normal tanpa Dompdf.

### Langkah 6: Akses Aplikasi

1. Buka web browser
2. Akses aplikasi melalui URL:
   ```
   http://localhost/manus_inventory
   ```
3. Anda akan otomatis diarahkan ke halaman Login

### Langkah 7: Registrasi dan Login

1. Klik link **"Daftar sekarang"** di halaman login
2. Isi form registrasi:
   - Nama Lengkap
   - Username (minimal 4 karakter)
   - Password (minimal 6 karakter)
   - Konfirmasi Password
3. Klik tombol **"Daftar"**
4. Setelah berhasil, login menggunakan username dan password yang telah dibuat

## Penggunaan

### Dashboard
- Melihat statistik total barang, kategori, stok, dan nilai inventaris
- Melihat 5 barang terbaru yang ditambahkan

### Data Barang
- **Tambah Barang:** Klik tombol "Tambah Barang" dan isi form
- **Edit Barang:** Klik icon pensil pada baris barang yang ingin diedit
- **Hapus Barang:** Klik icon tempat sampah (akan muncul konfirmasi)
- **Pencarian:** Gunakan kolom pencarian untuk mencari berdasarkan kode, nama, atau merek
- **Filter:** Filter barang berdasarkan kategori

### Kategori
- **Tambah Kategori:** Klik tombol "Tambah Kategori"
- **Edit Kategori:** Klik tombol edit pada kategori yang ingin diubah
- **Hapus Kategori:** Kategori hanya bisa dihapus jika tidak sedang digunakan oleh barang

### Laporan PDF
1. Klik menu **"Laporan"** di sidebar
2. Lihat preview data barang dan statistik
3. Klik tombol **"Cetak PDF"** untuk generate laporan
4. Laporan akan otomatis ter-download atau terbuka di tab baru

## Struktur Database

### Tabel: users
| Field | Type | Keterangan |
|-------|------|------------|
| id | INT(11) | Primary Key, Auto Increment |
| nama | VARCHAR(100) | Nama lengkap user |
| username | VARCHAR(50) | Username (unique) |
| password | VARCHAR(255) | Password terenkripsi |
| created_at | TIMESTAMP | Tanggal registrasi |

### Tabel: kategori
| Field | Type | Keterangan |
|-------|------|------------|
| id_kategori | INT(11) | Primary Key, Auto Increment |
| nama_kategori | VARCHAR(100) | Nama kategori (unique) |
| created_at | TIMESTAMP | Tanggal dibuat |

### Tabel: barang
| Field | Type | Keterangan |
|-------|------|------------|
| id_barang | INT(11) | Primary Key, Auto Increment |
| kode_barang | VARCHAR(30) | Kode barang (unique) |
| nama_barang | VARCHAR(150) | Nama barang |
| id_kategori | INT(11) | Foreign Key ke tabel kategori |
| merek | VARCHAR(100) | Merek barang |
| harga_beli | DECIMAL(15,2) | Harga beli |
| harga_jual | DECIMAL(15,2) | Harga jual |
| stok | INT(11) | Jumlah stok |
| satuan | VARCHAR(30) | Satuan (pcs, box, dll) |
| created_at | TIMESTAMP | Tanggal dibuat |
| updated_at | TIMESTAMP | Tanggal diupdate |

## Fitur Keamanan

- **Password Hashing:** Menggunakan `password_hash()` dengan algoritma bcrypt
- **Prepared Statements:** Semua query menggunakan prepared statements untuk mencegah SQL Injection
- **CSRF Protection:** Token CSRF pada semua form yang mengubah data
- **Session Management:** Regenerasi session ID setelah login
- **Input Validation:** Validasi data di sisi server dan client
- **Output Escaping:** Menggunakan `htmlspecialchars()` untuk mencegah XSS

## Troubleshooting

### Error: "Koneksi gagal"
- Pastikan MySQL sudah running di XAMPP
- Periksa konfigurasi database di `config/koneksi.php`
- Pastikan database `db_manus_inventory` sudah diimport

### Error: PDF tidak ter-generate / halaman instruksi muncul
- Pastikan folder `vendor/dompdf` ada di dalam project dan berisi file `autoload.inc.php`
- Cara pasang ulang manual ada di file `INSTALL_DOMPDF.md`

### Halaman blank atau error 500
- Cek error log di: `C:\xampp\apache\logs\error.log`
- Pastikan PHP extension yang diperlukan sudah aktif
- Pastikan file permissions sudah benar

### PDF tidak ter-generate
- Pastikan folder `vendor/dompdf` lengkap (lihat `INSTALL_DOMPDF.md`)
- Pastikan PHP extension `mbstring`, `gd`, dan `dom` aktif

## Testing

### Test Case Manual

1. **Test Login & Registrasi**
   - Registrasi user baru
   - Login dengan user yang baru dibuat
   - Test login dengan password salah
   - Test logout

2. **Test CRUD Barang**
   - Tambah barang baru
   - Edit barang yang sudah ada
   - Hapus barang
   - Pencarian dan filter barang

3. **Test CRUD Kategori**
   - Tambah kategori baru
   - Edit kategori
   - Test hapus kategori yang sedang digunakan (harus gagal)
   - Hapus kategori yang tidak digunakan

4. **Test Laporan PDF**
   - Generate laporan PDF
   - Cek isi PDF apakah data sudah benar

## Kredit

- **Developer:** Manus Inventory Team
- **Framework UI:** Bootstrap 5
- **Icons:** Bootstrap Icons
- **PDF Library:** Dompdf
- **Purpose:** Junior Web Developer Certification Project

## Lisensi

Project ini dibuat untuk keperluan sertifikasi Junior Web Developer.

## Kontak & Support

Untuk pertanyaan atau bantuan, silakan hubungi:
- Email: support@manusinventory.com
- Website: http://localhost/manus_inventory

---

**Selamat menggunakan Manus Inventory System!** 🚀
