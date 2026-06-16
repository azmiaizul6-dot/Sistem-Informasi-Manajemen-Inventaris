<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = (int)$_POST['produk_id'];
    $gudang_id = (int)$_POST['gudang_id'];
    $jumlah = (int)$_POST['jumlah'];
    $keterangan = escape($conn, $_POST['keterangan']);
    $no_referensi = escape($conn, $_POST['no_referensi']);

    $sql = "INSERT INTO stok_masuk (produk_id, gudang_id, jumlah, keterangan, no_referensi, user_id) 
            VALUES ($produk_id, $gudang_id, $jumlah, '$keterangan', '$no_referensi', {$_SESSION['user_id']})";
    
    if ($conn->query($sql)) {
        logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Stok Masuk', null, ['jumlah' => $jumlah]);
        header("Location: stok-masuk.php?success=Stok masuk berhasil dicatat");
        exit;
    }
}

$stok_masuk = fetchAll($conn, "SELECT sm.*, p.nama_produk, p.kode_produk, g.nama_gudang, u.nama_lengkap 
                                FROM stok_masuk sm 
                                JOIN produk p ON sm.produk_id = p.id 
                                JOIN gudang g ON sm.gudang_id = g.id 
                                JOIN users u ON sm.user_id = u.id 
                                ORDER BY sm.created_at DESC");
$produk = fetchAll($conn, "SELECT * FROM produk");
$gudang = fetchAll($conn, "SELECT * FROM gudang");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Masuk - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">📥 Stok Masuk</h1>
                <p class="text-white/70">Catat barang yang masuk ke gudang</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="card p-6 lg:col-span-1">
                    <h2 class="text-xl font-bold text-white mb-4">Catat Stok Masuk</h2>
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
                            <label class="label-base">No Referensi</label>
                            <input type="text" name="no_referensi" class="input-base" placeholder="e.g. INV-001">
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
                    <h2 class="text-xl font-bold text-white mb-4">Riwayat Stok Masuk</h2>
                    <div class="overflow-x-auto">
                        <table class="table-glass w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Produk</th>
                                    <th>Gudang</th>
                                    <th>Jumlah</th>
                                    <th>User</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stok_masuk as $sm): ?>
                                    <tr>
                                        <td><?php echo formatTanggal($sm['created_at'], 'd M Y H:i'); ?></td>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($sm['kode_produk']); ?></span>
                                            <br><span class="text-xs text-white/70"><?php echo htmlspecialchars($sm['nama_produk']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($sm['nama_gudang']); ?></td>
                                        <td class="font-bold text-emerald-400">+<?php echo $sm['jumlah']; ?></td>
                                        <td class="text-xs text-white/70"><?php echo htmlspecialchars($sm['nama_lengkap']); ?></td>
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