<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete
if ($action == 'delete' && $id > 0) {
    $conn->query("DELETE FROM users WHERE id = $id AND role = 'user'");
    logAktivitas($conn, $_SESSION['user_id'], 'DELETE', 'User', ['id' => $id]);
    header("Location: users.php?success=User berhasil dihapus");
    exit;
}

$users = fetchAll($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">👥 Manajemen User</h1>
                <p class="text-white/70">Kelola daftar user dan permission</p>
            </div>

            <div id="alert-container"></div>
            <?php if (isset($_GET['success'])): ?>
                <script>showAlert('<?php echo htmlspecialchars($_GET['success']); ?>', 'success');</script>
            <?php endif; ?>

            <div class="card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Daftar User (<?php echo count($users); ?>)</h2>
                    <input type="text" id="searchInput" placeholder="Cari user..." class="input-base" onkeyup="searchTable('searchInput', 'usersTable')">
                </div>

                <div class="overflow-x-auto">
                    <table class="table-glass w-full" id="usersTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Telepon</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Terdaftar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['nama_lengkap']); ?></td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($u['username']); ?></span></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <code class="bg-slate-900/50 px-2 py-1 rounded text-xs text-white/70"><?php echo htmlspecialchars($u['password']); ?></code>
                                    </td>
                                    <td><?php echo htmlspecialchars($u['no_telepon'] ?? '-'); ?></td>
                                    <td>
                                        <?php echo $u['role'] == 'admin' ? '<span class="badge badge-danger">👤 Admin</span>' : '<span class="badge badge-success">👤 User</span>'; ?>
                                    </td>
                                    <td>
                                        <?php echo $u['status'] == 'aktif' ? '<span class="badge badge-success">✓ Aktif</span>' : '<span class="badge badge-danger">✕ Nonaktif</span>'; ?>
                                    </td>
                                    <td class="text-xs text-white/70"><?php echo formatTanggal($u['created_at'], 'd M Y'); ?></td>
                                    <td>
                                        <?php if ($u['role'] != 'admin'): ?>
                                            <button onclick="if(confirmDelete('<?php echo htmlspecialchars($u['nama_lengkap']); ?>')) window.location.href='users.php?action=delete&id=<?php echo $u['id']; ?>';" class="text-red-400 hover:text-red-300 text-xs font-semibold">🗑️ Hapus</button>
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

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>