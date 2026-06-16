<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete
if ($action == 'delete' && $id > 0) {
    $conn->query("DELETE FROM kategori_produk WHERE id = $id");
    logAktivitas($conn, $_SESSION['user_id'], 'DELETE', 'Kategori', ['id' => $id]);
    header("Location: kategori.php?success=Kategori berhasil dihapus");
    exit;
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = escape($conn, $_POST['nama_kategori']);
    $deskripsi = escape($conn, $_POST['deskripsi']);

    if ($action == 'add') {
        $sql = "INSERT INTO kategori_produk (nama_kategori, deskripsi) VALUES ('$nama_kategori', '$deskripsi')";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Kategori', null, ['nama' => $nama_kategori]);
            header("Location: kategori.php?success=Kategori berhasil ditambahkan");
            exit;
        }
    } elseif ($action == 'edit' && $id > 0) {
        $sql = "UPDATE kategori_produk SET nama_kategori='$nama_kategori', deskripsi='$deskripsi' WHERE id=$id";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'UPDATE', 'Kategori', ['id' => $id], ['nama' => $nama_kategori]);
            header("Location: kategori.php?success=Kategori berhasil diperbarui");
            exit;
        }
    }
}

$kategori_data = null;
if ($action == 'edit' && $id > 0) {
    $kategori_data = fetchOne($conn, "SELECT * FROM kategori_produk WHERE id = $id");
}

if ($action == 'list') {
    $kategori = fetchAll($conn, "SELECT * FROM kategori_produk");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Produk - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">🏷️ Kategori Produk</h1>
                <p class="text-white/70">Kelola kategori produk</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <div class="card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-white">Daftar Kategori (<?php echo count($kategori); ?>)</h2>
                        <a href="kategori.php?action=add" class="btn btn-primary">➕ Tambah Kategori</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($kategori as $k): ?>
                            <div class="card-glass p-4">
                                <h3 class="text-lg font-bold text-white mb-2"><?php echo htmlspecialchars($k['nama_kategori']); ?></h3>
                                <p class="text-white/70 text-sm mb-4"><?php echo htmlspecialchars($k['deskripsi'] ?? '-'); ?></p>
                                <div class="flex gap-2">
                                    <a href="kategori.php?action=edit&id=<?php echo $k['id']; ?>" class="btn btn-sm btn-primary flex-1">✏️ Edit</a>
                                    <button onclick="if(confirmDelete('<?php echo htmlspecialchars($k['nama_kategori']); ?>')) window.location.href='kategori.php?action=delete&id=<?php echo $k['id']; ?>';" class="btn btn-sm btn-danger">🗑️</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card p-6 max-w-2xl">
                    <h2 class="text-2xl font-bold text-white mb-6">
                        <?php echo $action == 'add' ? '➕ Tambah Kategori' : '✏️ Edit Kategori'; ?>
                    </h2>

                    <form method="POST">
                        <div class="form-group">
                            <label class="label-base">Nama Kategori *</label>
                            <input type="text" name="nama_kategori" class="input-base" required value="<?php echo $kategori_data ? htmlspecialchars($kategori_data['nama_kategori']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label class="label-base">Deskripsi</label>
                            <textarea name="deskripsi" class="input-base h-24"><?php echo $kategori_data ? htmlspecialchars($kategori_data['deskripsi']) : ''; ?></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            <a href="kategori.php" class="btn btn-outline">❌ Batal</a>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>