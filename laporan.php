<?php
session_start();
require_once 'includes/auth.php';
require_once 'config/koneksi.php';
require_once 'includes/functions.php';

// Check if we need to generate PDF
if (isset($_GET['action']) && $_GET['action'] === 'cetak_pdf') {
    
    // Loader Dompdf tanpa Composer (paket manual dari GitHub releases)
    $dompdf_autoload = __DIR__ . '/vendor/dompdf/autoload.inc.php';
    $composer_autoload = __DIR__ . '/vendor/autoload.php';
    
    if (file_exists($composer_autoload)) {
        require_once $composer_autoload;
    } elseif (file_exists($dompdf_autoload)) {
        require_once $dompdf_autoload;
    } else {
        // Library Dompdf belum terpasang
        http_response_code(500);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Error</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>';
        echo '<div class="container mt-5"><div class="alert alert-danger"><h4>Library Dompdf Belum Terpasang</h4>';
        echo '<p>Folder <code>vendor/dompdf</code> tidak ditemukan. Unduh paket Dompdf dari ';
        echo '<a href="https://github.com/dompdf/dompdf/releases">GitHub Dompdf Releases</a> (file <code>dompdf-3.1.6.zip</code>), ';
        echo 'lalu extract ke: <code>' . htmlspecialchars(__DIR__ . '/vendor/dompdf') . '</code></p></div>';
        echo '<a href="laporan.php" class="btn btn-secondary">Kembali</a></div></body></html>';
        exit();
    }
    
    // Ambil semua data barang
    $barang_list = getAllBarang($conn);
    $stats = getDashboardStats($conn);
    
    // Configure Dompdf
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', false);
    
    $dompdf = new \Dompdf\Dompdf($options);
    
    // Create HTML for PDF
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Laporan Inventaris Barang</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 20px;
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                border-bottom: 3px solid #333;
                padding-bottom: 10px;
            }
            .header h1 {
                margin: 0;
                color: #333;
                font-size: 24px;
            }
            .header p {
                margin: 5px 0;
                color: #666;
            }
            .info-box {
                background: #f5f5f5;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
            }
            .info-box table {
                width: 100%;
            }
            .info-box td {
                padding: 5px;
            }
            table.data {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            table.data th {
                background: #667eea;
                color: white;
                padding: 10px;
                text-align: left;
                font-weight: bold;
            }
            table.data td {
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }
            table.data tr:nth-child(even) {
                background: #f9f9f9;
            }
            .footer {
                margin-top: 30px;
                text-align: right;
                font-size: 11px;
                color: #666;
            }
            .text-right {
                text-align: right;
            }
            .summary {
                margin-top: 20px;
                background: #e8f4f8;
                padding: 15px;
                border-radius: 5px;
            }
            .summary table {
                width: 100%;
            }
            .summary td {
                padding: 5px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>LAPORAN INVENTARIS BARANG</h1>
            <p>Manus Inventory System</p>
            <p>Tanggal Cetak: ' . date('d F Y, H:i:s') . '</p>
        </div>
        
        <div class="info-box">
            <table>
                <tr>
                    <td width="50%"><strong>Total Barang:</strong> ' . number_format($stats['total_barang']) . ' item</td>
                    <td width="50%"><strong>Total Kategori:</strong> ' . number_format($stats['total_kategori']) . ' kategori</td>
                </tr>
                <tr>
                    <td><strong>Total Stok:</strong> ' . number_format($stats['total_stok']) . ' unit</td>
                    <td><strong>Nilai Inventaris:</strong> ' . formatRupiah($stats['nilai_inventaris']) . '</td>
                </tr>
            </table>
        </div>
        
        <table class="data">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="10%">Kode</th>
                    <th width="20%">Nama Barang</th>
                    <th width="12%">Kategori</th>
                    <th width="12%">Merek</th>
                    <th width="12%">Harga Beli</th>
                    <th width="12%">Harga Jual</th>
                    <th width="8%">Stok</th>
                    <th width="9%">Satuan</th>
                </tr>
            </thead>
            <tbody>';
    
    if (count($barang_list) > 0) {
        foreach ($barang_list as $index => $item) {
            $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . htmlspecialchars($item['kode_barang']) . '</td>
                    <td>' . htmlspecialchars($item['nama_barang']) . '</td>
                    <td>' . htmlspecialchars($item['nama_kategori']) . '</td>
                    <td>' . htmlspecialchars($item['merek']) . '</td>
                    <td class="text-right">' . formatRupiah($item['harga_beli']) . '</td>
                    <td class="text-right">' . formatRupiah($item['harga_jual']) . '</td>
                    <td class="text-right">' . number_format($item['stok']) . '</td>
                    <td>' . htmlspecialchars($item['satuan']) . '</td>
                </tr>';
        }
    } else {
        $html .= '<tr><td colspan="9" style="text-align: center;">Tidak ada data</td></tr>';
    }
    
    $html .= '
            </tbody>
        </table>
        
        <div class="summary">
            <table>
                <tr>
                    <td width="70%">Total Nilai Inventaris (Harga Jual × Stok):</td>
                    <td width="30%" class="text-right">' . formatRupiah($stats['nilai_inventaris']) . '</td>
                </tr>
            </table>
        </div>
        
        <div class="footer">
            <p>Dicetak oleh: ' . htmlspecialchars($_SESSION['nama']) . '</p>
            <p>Dokumen ini digenerate secara otomatis oleh sistem</p>
        </div>
    </body>
    </html>';
    
    // Load HTML to Dompdf
    $dompdf->loadHtml($html);
    
    // Set paper size
    $dompdf->setPaper('A4', 'landscape');
    
    // Render PDF
    $dompdf->render();
    
    // Output PDF
    $dompdf->stream('Laporan_Inventaris_' . date('Ymd_His') . '.pdf', ['Attachment' => false]);
    exit();
}

$page_title = 'Laporan';

// Ambil data untuk preview
$barang_list = getAllBarang($conn);
$stats = getDashboardStats($conn);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2 class="mb-0">Laporan Inventaris</h2>
            <p class="text-muted">Preview dan cetak laporan</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="laporan.php?action=cetak_pdf" class="btn btn-danger" target="_blank">
                <i class="bi bi-file-pdf"></i> Cetak PDF
            </a>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Barang</h6>
                    <h3 class="mb-0"><?php echo number_format($stats['total_barang']); ?></h3>
                    <small>item</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Kategori</h6>
                    <h3 class="mb-0"><?php echo number_format($stats['total_kategori']); ?></h3>
                    <small>kategori</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Total Stok</h6>
                    <h3 class="mb-0"><?php echo number_format($stats['total_stok']); ?></h3>
                    <small>unit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <h6 class="text-muted">Nilai Inventaris</h6>
                    <h3 class="mb-0" style="font-size: 1.2rem;"><?php echo formatRupiah($stats['nilai_inventaris']); ?></h3>
                    <small>total nilai jual</small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Preview Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Preview Data Barang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-primary">
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
                                    <th>Total Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($barang_list) > 0): ?>
                                    <?php 
                                    $total_nilai = 0;
                                    foreach ($barang_list as $index => $item): 
                                        $nilai_item = $item['harga_jual'] * $item['stok'];
                                        $total_nilai += $nilai_item;
                                    ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($item['kode_barang']); ?></span></td>
                                            <td><?php echo htmlspecialchars($item['nama_barang']); ?></td>
                                            <td><?php echo htmlspecialchars($item['nama_kategori']); ?></td>
                                            <td><?php echo htmlspecialchars($item['merek']); ?></td>
                                            <td><?php echo formatRupiah($item['harga_beli']); ?></td>
                                            <td><?php echo formatRupiah($item['harga_jual']); ?></td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $item['stok'] < 10 ? 'bg-danger' : 'bg-success'; ?>">
                                                    <?php echo number_format($item['stok']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['satuan']); ?></td>
                                            <td><?php echo formatRupiah($nilai_item); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-info">
                                        <td colspan="9" class="text-end"><strong>Total Nilai Inventaris:</strong></td>
                                        <td><strong><?php echo formatRupiah($total_nilai); ?></strong></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Tidak ada data barang</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <p class="text-muted">Klik tombol "Cetak PDF" di atas untuk mengunduh laporan dalam format PDF</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
