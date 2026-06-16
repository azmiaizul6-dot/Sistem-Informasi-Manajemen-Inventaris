<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$audit = fetchAll($conn, "SELECT a.*, u.nama_lengkap FROM audit_trail a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Trail - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">📋 Audit Trail</h1>
                <p class="text-white/70">Pencatatan aktivitas pengguna</p>
            </div>

            <div class="card p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">Riwayat Aktivitas (<?php echo count($audit); ?> Terakhir)</h2>
                    <button onclick="exportToCSV('audit-trail.csv', 'auditTable')" class="btn btn-primary">📥 Export CSV</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table-glass w-full text-sm" id="auditTable">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Aksi</th>
                                <th>Modul</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($audit as $a): ?>
                                <tr>
                                    <td><?php echo formatTanggal($a['created_at'], 'd M Y H:i:s'); ?></td>
                                    <td><?php echo htmlspecialchars($a['nama_lengkap']); ?></td>
                                    <td>
                                        <?php 
                                        $badge_class = [
                                            'CREATE' => 'badge-success',
                                            'UPDATE' => 'badge-info',
                                            'DELETE' => 'badge-danger',
                                            'LOGIN' => 'badge-info',
                                            'LOGOUT' => 'badge-warning',
                                            'APPROVE' => 'badge-success'
                                        ][$a['aksi']] ?? 'badge-pending';
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo $a['aksi']; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($a['modul']); ?></td>
                                    <td class="font-mono text-xs"><?php echo htmlspecialchars($a['ip_address']); ?></td>
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