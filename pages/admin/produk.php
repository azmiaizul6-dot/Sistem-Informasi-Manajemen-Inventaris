<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Handle Delete
if ($action == 'delete' && $id > 0) {
    $conn->query("DELETE FROM produk WHERE id = $id");
    logAktivitas($conn, $_SESSION['user_id'], 'DELETE', 'Produk', ['id' => $id]);
    header("Location: produk.php?success=Produk berhasil dihapus");
    exit;
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = escape($conn, $_POST['nama_produk']);
    $kode_produk = escape($conn, $_POST['kode_produk']);
    $kategori_id = (int)$_POST['kategori_id'];
    $harga = (float)$_POST['harga'];
    $stok_total = (int)$_POST['stok_total'];
    $stok_minimum = (int)$_POST['stok_minimum'];
    $deskripsi = escape($conn, $_POST['deskripsi']);

    if ($action == 'add') {
        $sql = "INSERT INTO produk (kode_produk, nama_produk, kategori_id, harga, stok_total, stok_minimum, deskripsi) 
                VALUES ('$kode_produk', '$nama_produk', $kategori_id, $harga, $stok_total, $stok_minimum, '$deskripsi')";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Produk', null, ['nama' => $nama_produk]);
            header("Location: produk.php?success=Produk berhasil ditambahkan");
            exit;
        }
    } elseif ($action == 'edit' && $id > 0) {
        $sql = "UPDATE produk SET nama_produk='$nama_produk', kategori_id=$kategori_id, harga=$harga, 
                stok_total=$stok_total, stok_minimum=$stok_minimum, deskripsi='$deskripsi' WHERE id=$id";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'UPDATE', 'Produk', ['id' => $id], ['nama' => $nama_produk]);
            header("Location: produk.php?success=Produk berhasil diperbarui");
            exit;
        }
    }
}

// Get data
$kategori = fetchAll($conn, "SELECT * FROM kategori_produk");
$produk_data = null;

if ($action == 'edit' && $id > 0) {
    $produk_data = fetchOne($conn, "SELECT * FROM produk WHERE id = $id");
    if (!$produk_data) {
        header("Location: produk.php");
        exit;
    }
}

if ($action == 'list') {
    $produk = fetchAll($conn, "SELECT p.*, k.nama_kategori FROM produk p JOIN kategori_produk k ON p.kategori_id = k.id");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Produk - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Master Produk</h1>
                <p class="text-white/70">Kelola data produk, kategori, dan stok</p>
            </div>

            <!-- Alert -->
            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <!-- List View -->
                <div class="card p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-white">Daftar Produk</h2>
                            <p class="text-white/70 text-sm">Total: <?php echo count($produk); ?> produk</p>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="searchInput" placeholder="Cari produk..." class="input-base" onkeyup="searchTable('searchInput', 'produkTable')">
                            <a href="produk.php?action=add" class="btn btn-primary">➕ Tambah Produk</a>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="table-glass w-full" id="produkTable">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Min</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produk as $p): ?>
                                    <tr>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($p['kode_produk']); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['nama_produk']); ?></td>
                                        <td><?php echo htmlspecialchars($p['nama_kategori']); ?></td>
                                        <td><?php echo formatRupiah($p['harga']); ?></td>
                                        <td><span class="font-bold text-white"><?php echo $p['stok_total']; ?></span></td>
                                        <td><?php echo $p['stok_minimum']; ?></td>
                                        <td>
                                            <?php if ($p['stok_total'] > $p['stok_minimum']): ?>
                                                <span class="badge badge-success">✓ Normal</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">✕ Minimum</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="flex gap-2">
                                                <a href="produk.php?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">✏️</a>
                                                <button onclick="if(confirmDelete('<?php echo htmlspecialchars($p['nama_produk']); ?>')) window.location.href='produk.php?action=delete&id=<?php echo $p['id']; ?>';" class="btn btn-sm btn-danger">🗑️</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <!-- Form Add/Edit -->
                <div class="card p-6 max-w-2xl">
                    <h2 class="text-2xl font-bold text-white mb-6">
                        <?php echo $action == 'add' ? '➕ Tambah Produk Baru' : '✏️ Edit Produk'; ?>
                    </h2>

                    <form method="POST">
                        <!-- Kode Produk -->
                        <div class="form-group">
                            <label class="label-base">Kode Produk *</label>
                            <input type="text" name="kode_produk" class="input-base" required value="<?php echo $produk_data ? htmlspecialchars($produk_data['kode_produk']) : ''; ?>">
                        </div>

                        <!-- Nama Produk -->
                        <div class="form-group">
                            <label class="label-base">Nama Produk *</label>
                            <input type="text" name="nama_produk" class="input-base" required value="<?php echo $produk_data ? htmlspecialchars($produk_data['nama_produk']) : ''; ?>">
                        </div>

                        <!-- Kategori -->
                        <div class="form-group">
                            <label class="label-base">Kategori *</label>
                            <select name="kategori_id" class="input-base" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?php echo $k['id']; ?>" <?php echo $produk_data && $produk_data['kategori_id'] == $k['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <!-- Harga -->
                            <div class="form-group">
                                <label class="label-base">Harga *</label>
                                <input type="number" name="harga" class="input-base" required step="0.01" value="<?php echo $produk_data ? $produk_data['harga'] : ''; ?>">
                            </div>

                            <!-- Stok Total -->
                            <div class="form-group">
                                <label class="label-base">Stok Total *</label>
                                <input type="number" name="stok_total" class="input-base" required value="<?php echo $produk_data ? $produk_data['stok_total'] : '0'; ?>">
                            </div>
                        </div>

                        <!-- Stok Minimum -->
                        <div class="form-group">
                            <label class="label-base">Stok Minimum *</label>
                            <input type="number" name="stok_minimum" class="input-base" required value="<?php echo $produk_data ? $produk_data['stok_minimum'] : '5'; ?>">
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label class="label-base">Deskripsi</label>
                            <textarea name="deskripsi" class="input-base h-24"><?php echo $produk_data ? htmlspecialchars($produk_data['deskripsi']) : ''; ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $action == 'add' ? '💾 Tambahkan' : '💾 Perbarui'; ?>
                            </button>
                            <a href="produk.php" class="btn btn-outline">❌ Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Topbar -->
    <?php include '../../../includes/topbar.php'; ?>

    <script src="../../../assets/js/main.js"></script>
</body>
</html>