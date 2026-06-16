<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$settings = fetchOne($conn, "SELECT * FROM settings LIMIT 1");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <?php include '../../../includes/sidebar.php'; ?>

    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">⚙️ Pengaturan Sistem</h1>
                <p class="text-white/70">Konfigurasi sistem dan preferensi</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Settings Form -->
                <div class="card p-6 lg:col-span-2">
                    <h2 class="text-2xl font-bold text-white mb-6">Konfigurasi Umum</h2>
                    
                    <form method="POST" class="space-y-6">
                        <div class="form-group">
                            <label class="label-base">Nama Sistem</label>
                            <input type="text" name="nama_sistem" class="input-base" value="<?php echo htmlspecialchars($settings['nama_sistem']); ?>">
                        </div>

                        <div class="form-group">
                            <label class="label-base">Deskripsi</label>
                            <textarea name="deskripsi" class="input-base h-24"><?php echo htmlspecialchars($settings['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="label-base">Warna Utama</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="primary_color" class="w-12 h-10 rounded cursor-pointer" value="<?php echo htmlspecialchars($settings['primary_color']); ?>">
                                    <input type="text" name="primary_color_text" class="input-base" value="<?php echo htmlspecialchars($settings['primary_color']); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="label-base">Warna Sekunder</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="secondary_color" class="w-12 h-10 rounded cursor-pointer" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>">
                                    <input type="text" name="secondary_color_text" class="input-base" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="btn btn-primary">💾 Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>

                <!-- Info Box -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">ℹ️ Informasi Sistem</h3>
                    
                    <div class="space-y-4 text-sm text-white/70">
                        <div>
                            <p class="font-semibold text-white">PHP Version</p>
                            <p><?php echo phpversion(); ?></p>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Database</p>
                            <p>MySQL / MariaDB</p>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Framework</p>
                            <p>PHP Native + Tailwind CSS</p>
                        </div>
                        <div>
                            <p class="font-semibold text-white">Version</p>
                            <p>1.0.0</p>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-white/10">
                        <p class="text-xs text-white/50 text-center">Sistem Manajemen Inventaris &copy; 2024</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../includes/topbar.php'; ?>
    <script src="../../../assets/js/main.js"></script>
</body>
</html>