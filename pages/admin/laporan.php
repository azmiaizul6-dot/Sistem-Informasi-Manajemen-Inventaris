<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">📊 Laporan & Export</h1>
                <p class="text-white/70">Generate laporan dan export data</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Laporan Stok -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">📦 Laporan Stok Produk</h3>
                    <p class="text-white/70 mb-4">Export daftar produk beserta stok dari semua gudang</p>
                    <button onclick="exportToCSV('laporan-stok.csv', 'tabelStok')" class="btn btn-primary w-full">📥 Export CSV</button>
                </div>

                <!-- Laporan Gudang -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">🏢 Laporan Data Gudang</h3>
                    <p class="text-white/70 mb-4">Export data gudang dan PIC</p>
                    <button onclick="exportToCSV('laporan-gudang.csv', 'tabelGudang')" class="btn btn-primary w-full">📥 Export CSV</button>
                </div>
            </div>

            <!-- Stok Table (Hidden for export) -->
            <table id="tabelStok" style="display:none;">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok Total</th>
                        <th>Stok Minimum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $produk = fetchAll($conn, "SELECT p.*, k.nama_kategori FROM produk p JOIN kategori_produk k ON p.kategori_id = k.id");
                    foreach ($produk as $p): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['kode_produk']); ?></td>
                        <td><?php echo htmlspecialchars($p['nama_produk']); ?></td>
                        <td><?php echo htmlspecialchars($p['nama_kategori']); ?></td>
                        <td><?php echo $p['harga']; ?></td>
                        <td><?php echo $p['stok_total']; ?></td>
                        <td><?php echo $p['stok_minimum']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Gudang Table (Hidden for export) -->
            <table id="tabelGudang" style="display:none;">
                <thead>
                    <tr>
                        <th>Kode Gudang</th>
                        <th>Nama Gudang</th>
                        <th>Alamat</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>No Telepon</th>
                        <th>PIC Nama</th>
                        <th>PIC Telepon</th>
                        <th>Kapasitas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $gudang = fetchAll($conn, "SELECT * FROM gudang");
                    foreach ($gudang as $g): 
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($g['kode_gudang']); ?></td>
                        <td><?php echo htmlspecialchars($g['nama_gudang']); ?></td>
                        <td><?php echo htmlspecialchars($g['alamat']); ?></td>
                        <td><?php echo htmlspecialchars($g['kota']); ?></td>
                        <td><?php echo htmlspecialchars($g['provinsi']); ?></td>
                        <td><?php echo htmlspecialchars($g['no_telepon']); ?></td>
                        <td><?php echo htmlspecialchars($g['pic_nama']); ?></td>
                        <td><?php echo htmlspecialchars($g['pic_telepon']); ?></td>
                        <td><?php echo $g['kapasitas']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>