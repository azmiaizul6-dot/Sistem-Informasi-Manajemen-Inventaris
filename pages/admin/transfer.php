<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produk_id = (int)$_POST['produk_id'];
    $gudang_asal_id = (int)$_POST['gudang_asal_id'];
    $gudang_tujuan_id = (int)$_POST['gudang_tujuan_id'];
    $jumlah = (int)$_POST['jumlah'];
    $keterangan = escape($conn, $_POST['keterangan']);
    $status = 'pending';

    $sql = "INSERT INTO transfer_stok (produk_id, gudang_asal_id, gudang_tujuan_id, jumlah, keterangan, status, user_id) 
            VALUES ($produk_id, $gudang_asal_id, $gudang_tujuan_id, $jumlah, '$keterangan', '$status', {$_SESSION['user_id']})";
    
    if ($conn->query($sql)) {
        logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Transfer Stok', null, ['jumlah' => $jumlah]);
        header("Location: transfer.php?success=Transfer berhasil dibuat");
        exit;
    }
}

if ($action == 'approve' && $id > 0) {
    $conn->query("UPDATE transfer_stok SET status='selesai' WHERE id=$id");
    logAktivitas($conn, $_SESSION['user_id'], 'APPROVE', 'Transfer Stok', ['id' => $id]);
    header("Location: transfer.php?success=Transfer berhasil disetujui");
    exit;
}

$transfer = fetchAll($conn, "SELECT ts.*, p.nama_produk, p.kode_produk, g1.nama_gudang as nama_gudang_asal, g2.nama_gudang as nama_gudang_tujuan, u.nama_lengkap 
                             FROM transfer_stok ts 
                             JOIN produk p ON ts.produk_id = p.id 
                             JOIN gudang g1 ON ts.gudang_asal_id = g1.id 
                             JOIN gudang g2 ON ts.gudang_tujuan_id = g2.id 
                             JOIN users u ON ts.user_id = u.id 
                             ORDER BY ts.created_at DESC");
$produk = fetchAll($conn, "SELECT * FROM produk");
$gudang = fetchAll($conn, "SELECT * FROM gudang");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Stok - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">🚚 Transfer Stok</h1>
                <p class="text-white/70">Transfer barang antar gudang</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Form -->
                <div class="card p-6 lg:col-span-1">
                    <h2 class="text-xl font-bold text-white mb-4">Buat Transfer</h2>
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
                            <label class="label-base">Gudang Asal *</label>
                            <select name="gudang_asal_id" class="input-base" required>
                                <option value="">-- Pilih Gudang --</option>
                                <?php foreach ($gudang as $g): ?>
                                    <option value="<?php echo $g['id']; ?>"><?php echo htmlspecialchars($g['nama_gudang']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="label-base">Gudang Tujuan *</label>
                            <select name="gudang_tujuan_id" class="input-base" required>
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
                            <label class="label-base">Keterangan</label>
                            <textarea name="keterangan" class="input-base h-20"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">💾 Buat Transfer</button>
                    </form>
                </div>

                <!-- List -->
                <div class="card p-6 lg:col-span-2">
                    <h2 class="text-xl font-bold text-white mb-4">Daftar Transfer</h2>
                    <div class="overflow-x-auto">
                        <table class="table-glass w-full text-sm">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Dari → Ke</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transfer as $t): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-info"><?php echo htmlspecialchars($t['kode_produk']); ?></span>
                                            <br><span class="text-xs text-white/70"><?php echo htmlspecialchars($t['nama_produk']); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-xs"><?php echo htmlspecialchars($t['nama_gudang_asal']); ?></span><br>
                                            <span class="text-xs">→ <?php echo htmlspecialchars($t['nama_gudang_tujuan']); ?></span>
                                        </td>
                                        <td><?php echo $t['jumlah']; ?></td>
                                        <td>
                                            <?php 
                                            $badge_class = [
                                                'pending' => 'badge-warning',
                                                'diproses' => 'badge-info',
                                                'selesai' => 'badge-success',
                                                'dibatalkan' => 'badge-danger'
                                            ][$t['status']] ?? 'badge-pending';
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($t['status']); ?></span>
                                        </td>
                                        <td>
                                            <?php if ($t['status'] == 'pending'): ?>
                                                <a href="transfer.php?action=approve&id=<?php echo $t['id']; ?>" class="text-green-400 hover:text-green-300 text-xs font-semibold">✓ Setujui</a>
                                            <?php endif; ?>
                                        </td>
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