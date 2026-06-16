<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$pengajuan = fetchAll($conn, "SELECT pp.*, p.nama_produk, p.kode_produk, g.nama_gudang, u.nama_lengkap as peminjam_nama, au.nama_lengkap as admin_nama
                               FROM pengajuan_peminjaman pp
                               JOIN produk p ON pp.produk_id = p.id
                               JOIN gudang g ON pp.gudang_id = g.id
                               JOIN users u ON pp.user_id = u.id
                               LEFT JOIN users au ON pp.disetujui_oleh = au.id
                               ORDER BY pp.created_at DESC");

// Handle Approve
if (isset($_GET['action']) && $_GET['action'] == 'approve' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $conn->query("UPDATE pengajuan_peminjaman SET status='disetujui', tanggal_persetujuan=NOW(), disetujui_oleh={$_SESSION['user_id']} WHERE id=$id");
    logAktivitas($conn, $_SESSION['user_id'], 'APPROVE', 'Pengajuan Peminjaman', ['id' => $id]);
    header("Location: pengajuan-peminjaman.php?success=Pengajuan disetujui");
    exit;
}

// Handle Reject
if (isset($_GET['action']) && $_GET['action'] == 'reject' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $keterangan = isset($_GET['keterangan']) ? escape($conn, $_GET['keterangan']) : 'Ditolak oleh admin';
    $conn->query("UPDATE pengajuan_peminjaman SET status='ditolak', keterangan_penolakan='$keterangan' WHERE id=$id");
    logAktivitas($conn, $_SESSION['user_id'], 'REJECT', 'Pengajuan Peminjaman', ['id' => $id]);
    header("Location: pengajuan-peminjaman.php?success=Pengajuan ditolak");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Peminjaman - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">📋 Pengajuan Peminjaman</h1>
                <p class="text-white/70">Kelola pengajuan peminjaman barang dari user</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <div class="card p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Daftar Pengajuan (<?php echo count($pengajuan); ?>)</h2>
                
                <div class="space-y-4">
                    <?php foreach ($pengajuan as $p): ?>
                        <div class="card-glass p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-white/70 text-sm">No Pengajuan</p>
                                    <p class="font-bold text-white"><?php echo htmlspecialchars($p['no_pengajuan']); ?></p>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">Peminjam</p>
                                    <p class="font-bold text-white"><?php echo htmlspecialchars($p['peminjam_nama']); ?></p>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">Produk</p>
                                    <p class="font-bold text-white"><?php echo htmlspecialchars($p['nama_produk']); ?> (<?php echo $p['jumlah']; ?> unit)</p>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">Gudang</p>
                                    <p class="font-bold text-white"><?php echo htmlspecialchars($p['nama_gudang']); ?></p>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">Tujuan</p>
                                    <p class="font-bold text-white"><?php echo htmlspecialchars($p['tujuan_peminjaman']); ?></p>
                                </div>
                                <div>
                                    <p class="text-white/70 text-sm">Status</p>
                                    <?php 
                                    $badge_class = [
                                        'pending' => 'badge-warning',
                                        'disetujui' => 'badge-success',
                                        'ditolak' => 'badge-danger',
                                        'dikembalikan' => 'badge-info'
                                    ][$p['status']] ?? 'badge-pending';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($p['status']); ?></span>
                                </div>
                            </div>

                            <?php if ($p['status'] == 'pending'): ?>
                                <div class="flex gap-2 pt-4 border-t border-white/10">
                                    <a href="pengajuan-peminjaman.php?action=approve&id=<?php echo $p['id']; ?>" class="btn btn-secondary text-sm">✓ Setujui</a>
                                    <button onclick="prompt('Alasan penolakan:', '') && (window.location.href='pengajuan-peminjaman.php?action=reject&id=<?php echo $p['id']; ?>&keterangan=' + encodeURIComponent(prompt('Alasan penolakan:', '')))" class="btn btn-danger text-sm">✕ Tolak</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>