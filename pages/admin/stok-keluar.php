<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = (int)$_POST['produk_id'];
    $gudang_id = (int)$_POST['gudang_id'];
    $jumlah = (int)$_POST['jumlah'];
    $tujuan = escape($conn, $_POST['tujuan']);
    $keterangan = escape($conn, $_POST['keterangan']);
    $no_referensi = escape($conn, $_POST['no_referensi']);

    $sql = "INSERT INTO stok_keluar (produk_id, gudang_id, jumlah, tujuan, keterangan, no_referensi, user_id) 
            VALUES ($produk_id, $gudang_id, $jumlah, '$tujuan', '$keterangan', '$no_referensi', {$_SESSION['user_id']})";
    
    if ($conn->query($sql)) {
        logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Stok Keluar', null, ['jumlah' => $jumlah]);
        header("Location: stok-keluar.php?success=Stok keluar berhasil dicatat");
        exit;
    }
}

$stok_keluar = fetchAll($conn, "SELECT sk.*, p.nama_produk, p.kode_produk, g.nama_gudang, u.nama_lengkap 
                                FROM stok_keluar sk 
                                JOIN produk p ON sk.produk_id = p.id 
                                JOIN gudang g ON sk.gudang_id = g.id 
                                JOIN users u ON sk.user_id = u.id 
                                ORDER BY sk.created_at DESC");
$produk = fetchAll($conn, "SELECT * FROM produk");
$gudang = fetchAll($conn, "SELECT * FROM gudang");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Keluar - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">📤 Stok Keluar</h1>
                <p class="text-white/70">Catat barang yang keluar dari gudang</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="card p-6 lg:col-span-1">
                    <h2 class="text-xl font-bold text-white mb-4">Catat Stok Keluar</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label class="label-base">Produk *</label>
                            <select name="produk_id" class="input-base" required>
                                <option value="">-- Pilih Produk --</option>
                                <?php foreach ($produk as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['nama_produk']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="label-base">Gudang *</label>
                            <select name="gudang_id" class="input-base" required>
                                <option value="">-- Pilih Gudang --</option>
                                <?php foreach ($gudang as $g): ?>
                                    <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama_gudang']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="label-base">Jumlah *</label>
                            <input type="number" name="jumlah" class="input-base" required min="1">
                        </div>

                        <div class="form-group">
                            <label class="label-base">Tujuan</label>
                            <input type="text" name="tujuan" class="input-base" placeholder="e.g. Dijual, Rusak, dll">
                        </div>

                        <div class="form-group">
                            <label class="label-base">No Referensi</label>
                            <input type="text" name="no_referensi" class="input-base" placeholder="e.g. PO-001">
                        </div>

                        <div class="form-group">
                            <label class="label-base">Keterangan</label>
                            <textarea name="keterangan" class="input-base h-20"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">💾 Simpan</button>
                    </form>
                </div>

                <!-- History -->
                <div class="card p-6 lg:col-span-2">
                    <h2 class="text-xl font-bold text-white mb-4">Riwayat Stok Keluar</h2>
                    <div class="overflow-x-auto">
                        <table class="table-glass w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Gudang</th>
                                    <th>Jumlah</th>
                                    <th>Tujuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_keluar as $sk): ?>
                                    <tr>
                                        <td><?php echo formatTanggal($sk['created_at'], 'd M Y H:i'); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($sk['kode_produk']); ?></span>
                                            <br><span class="text-xs text-white/70"><?php echo htmlspecialchars($sk['nama_produk']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($sk['nama_gudang']); ?></td>
                                        <td class="font-bold text-red-400">-<?php echo $sk['jumlah']; ?></td>
                                        <td><?php echo htmlspecialchars($sk['tujuan']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>