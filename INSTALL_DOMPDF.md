# PANDUAN INSTALASI DOMPDF (TANPA COMPOSER)

Aplikasi ini tidak memerlukan Composer. Dompdf dipasang secara manual dari paket resmi GitHub.

## Langkah Install Dompdf Manual

### 1. Download paket Dompdf

Buka halaman: https://github.com/dompdf/dompdf/releases

Download file paket (bukan "Source code"):
- `dompdf-3.1.6.zip` (atau versi terbaru yang tersedia)

> Catatan: Paket ini sudah berisi semua dependency (php-font-lib, php-svg-lib, masterminds/html5, dll).
> Jangan download tombol "Source code (zip)" karena tidak berisi dependencies.

### 2. Extract file

Extract file `dompdf-3.1.6.zip`. Akan muncul folder:

```
dompdf/
├── autoload.inc.php
├── vendor/
│   ├── autoload.php
│   ├── dompdf/
│   ├── masterminds/
│   ├── sabberworm/
│   └── composer/
└── ...
```

### 3. Copy ke folder project

Copy folder hasil extract ke dalam folder project:

```
C:\Users\Romanus\Downloads\xampp\htdocs\manus_inventory\vendor\dompdf\
```

Setelah selesai, struktur harus seperti ini (verifikasi file ada):

```
manus_inventory/
├── vendor/
│   └── dompdf/
│       ├── autoload.inc.php        <- WAJIB ADA
│       └── vendor/
│           └── autoload.php        <- WAJIB ADA
└── ...
```

### 4. Selesai

Buka halaman **Laporan** di aplikasi dan klik tombol **"Cetak PDF"**.
Jika Dompdf terpasang dengan benar, file PDF akan tergenerate.

Jika Dompdf belum terpasang, aplikasi akan menampilkan pesan instruksi
yang jelas (bukan error blank) — jadi tidak akan merusak halaman lain.

## Cara Kerja Loader

File `laporan.php` mencoba load library dengan urutan:

1. `vendor/autoload.php` (jika someday pakai Composer)
2. `vendor/dompdf/autoload.inc.php` (paket manual) ← cara ini

## Troubleshooting

### Tombol Cetak PDF memunculkan halaman instruksi
- Artinya `vendor/dompdf/autoload.inc.php` tidak ditemukan.
- Periksa kembali lokasi extract pada langkah 3.

### PDF kosong / error saat render
- Pastikan ekstensi PHP `mbstring`, `gd`, dan `dom` aktif.
- Cek di `http://localhost/dashboard` menggunakan `phpinfo()`.

### Butuh Composer di masa depan (opsional)
- File `composer.json` tetap tersedia jika ingin migrasi:
  `composer install` → loader otomatis pakai `vendor/autoload.php`.
