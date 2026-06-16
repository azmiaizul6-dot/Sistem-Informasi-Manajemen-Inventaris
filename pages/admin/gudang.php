<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete
if ($action == 'delete' && $id > 0) {
    $conn->query("DELETE FROM gudang WHERE id = $id");
    logAktivitas($conn, $_SESSION['user_id'], 'DELETE', 'Gudang', ['id' => $id]);
    header("Location: gudang.php?success=Gudang berhasil dihapus");
    exit;
}

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_gudang = escape($conn, $_POST['kode_gudang']);
    $nama_gudang = escape($conn, $_POST['nama_gudang']);
    $alamat = escape($conn, $_POST['alamat']);
    $kota = escape($conn, $_POST['kota']);
    $provinsi = escape($conn, $_POST['provinsi']);
    $no_telepon = escape($conn, $_POST['no_telepon']);
    $pic_nama = escape($conn, $_POST['pic_nama']);
    $pic_telepon = escape($conn, $_POST['pic_telepon']);
    $kapasitas = (int)$_POST['kapasitas'];

    if ($action == 'add') {
        $sql = "INSERT INTO gudang (kode_gudang, nama_gudang, alamat, kota, provinsi, no_telepon, pic_nama, pic_telepon, kapasitas) 
                VALUES ('$kode_gudang', '$nama_gudang', '$alamat', '$kota', '$provinsi', '$no_telepon', '$pic_nama', '$pic_telepon', $kapasitas)";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'CREATE', 'Gudang', null, ['nama' => $nama_gudang]);
            header("Location: gudang.php?success=Gudang berhasil ditambahkan");
            exit;
        }
    } elseif ($action == 'edit' && $id > 0) {
        $sql = "UPDATE gudang SET kode_gudang='$kode_gudang', nama_gudang='$nama_gudang', alamat='$alamat', kota='$kota', 
                provinsi='$provinsi', no_telepon='$no_telepon', pic_nama='$pic_nama', pic_telepon='$pic_telepon', kapasitas=$kapasitas WHERE id=$id";
        if ($conn->query($sql)) {
            logAktivitas($conn, $_SESSION['user_id'], 'UPDATE', 'Gudang', ['id' => $id], ['nama' => $nama_gudang]);
            header("Location: gudang.php?success=Gudang berhasil diperbarui");
            exit;
        }
    }
}

$gudang_data = null;
if ($action == 'edit' && $id > 0) {
    $gudang_data = fetchOne($conn, "SELECT * FROM gudang WHERE id = $id");
}

if ($action == 'list') {
    $gudang = fetchAll($conn, "SELECT * FROM gudang");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Gudang - Sistem Manajemen Inventaris</title>
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
                <h1 class="text-4xl font-bold text-white mb-2">Daftar Gudang</h1>
                <p class="text-white/70">Kelola lokasi gudang dan PIC</p>
            </div>

            <!-- Alert -->
            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <?php if ($action == 'list'): ?>
                <!-- List View -->
                <div class="card p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-white">Data Gudang</h2>
                        <a href="gudang.php?action=add" class="btn btn-primary">➕ Tambah Gudang</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php foreach ($gudang as $g): ?>
                            <div class="card-glass p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-bold text-white"><?php echo htmlspecialchars($g['nama_gudang']); ?></h3>
                                        <p class="text-white/70 text-sm"><?php echo htmlspecialchars($g['kode_gudang']); ?></p>
                                    </div>
                                    <span class="badge badge-info"><?php echo htmlspecialchars($g['kota']); ?></span>
                                </div>

                                <div class="space-y-2 mb-4 text-sm text-white/70">
                                    <p>📍 <?php echo htmlspecialchars($g['alamat']); ?></p>
                                    <p>📞 <?php echo htmlspecialchars($g['no_telepon']); ?></p>
                                    <p>👤 <?php echo htmlspecialchars($g['pic_nama']); ?> (<?php echo htmlspecialchars($g['pic_telepon']); ?>)</p>
                                    <p>📦 Kapasitas: <?php echo number_format($g['kapasitas']); ?> unit</p>
                                </div>

                                <div class="flex gap-2">
                                    <a href="gudang.php?action=edit&id=<?php echo $g['id']; ?>" class="btn btn-sm btn-primary flex-1">✏️ Edit</a>
                                    <button onclick="if(confirmDelete('<?php echo htmlspecialchars($g['nama_gudang']); ?>')) window.location.href='gudang.php?action=delete&id=<?php echo $g['id']; ?>';" class="btn btn-sm btn-danger">🗑️</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Form Add/Edit -->
                <div class="card p-6 max-w-2xl">
                    <h2 class="text-2xl font-bold text-white mb-6">
                        <?php echo $action == 'add' ? '➕ Tambah Gudang Baru' : '✏️ Edit Gudang'; ?>
                    </h2>

                    <form method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="label-base">Kode Gudang *</label>
                                <input type="text" name="kode_gudang" class="input-base" required value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['kode_gudang']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label class="label-base">Nama Gudang *</label>
                                <input type="text" name="nama_gudang" class="input-base" required value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['nama_gudang']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="label-base">Alamat *</label>
                            <textarea name="alamat" class="input-base h-20" required><?php echo $gudang_data ? htmlspecialchars($gudang_data['alamat']) : ''; ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="label-base">Kota *</label>
                                <input type="text" name="kota" class="input-base" required value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['kota']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label class="label-base">Provinsi *</label>
                                <input type="text" name="provinsi" class="input-base" required value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['provinsi']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="label-base">No Telepon</label>
                            <input type="tel" name="no_telepon" class="input-base" value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['no_telepon']) : ''; ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="label-base">Nama PIC *</label>
                                <input type="text" name="pic_nama" class="input-base" required value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['pic_nama']) : ''; ?>">
                            </div>
                            <div class="form-group">
                                <label class="label-base">No Telepon PIC</label>
                                <input type="tel" name="pic_telepon" class="input-base" value="<?php echo $gudang_data ? htmlspecialchars($gudang_data['pic_telepon']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="label-base">Kapasitas (unit) *</label>
                            <input type="number" name="kapasitas" class="input-base" required value="<?php echo $gudang_data ? $gudang_data['kapasitas'] : '0'; ?>">
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="btn btn-primary">💾 Simpan</button>
                            <a href="gudang.php" class="btn btn-outline">❌ Batal</a>
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