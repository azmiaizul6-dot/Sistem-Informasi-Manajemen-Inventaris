<?php
require_once '../../../config/database.php';
require_once '../../../config/session.php';

requireLogin();
requireAdmin();

$stats = getStatistikDashboard($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../../assets/css/tailwind.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include '../../../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="lg:ml-64 pt-20 px-4 pb-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8 animate-fade-in">
                <h1 class="text-4xl font-bold text-white mb-2">Dashboard Admin</h1>
                <p class="text-white/70">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>! 👋</p>
            </div>

            <!-- Alert Container -->
            <div id="alert-container"></div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Produk -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">Total Produk</p>
                            <p class="text-4xl font-bold text-blue-400 mt-2"><?php echo $stats['total_produk']; ?></p>
                        </div>
                        <span class="text-3xl">📦</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">Produk aktif dalam sistem</p>
                </div>

                <!-- Total Stok -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">Total Stok</p>
                            <p class="text-4xl font-bold text-emerald-400 mt-2"><?php echo $stats['total_stok']; ?></p>
                        </div>
                        <span class="text-3xl">📊</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">Unit stok tersedia</p>
                </div>

                <!-- Total Gudang -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">Total Gudang</p>
                            <p class="text-4xl font-bold text-cyan-400 mt-2"><?php echo $stats['total_gudang']; ?></p>
                        </div>
                        <span class="text-3xl">🏢</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">Lokasi gudang aktif</p>
                </div>

                <!-- Stok Minimum -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">⚠️ Stok Minimum</p>
                            <p class="text-4xl font-bold text-amber-400 mt-2"><?php echo $stats['stok_minimum']; ?></p>
                        </div>
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">Produk perlu restock</p>
                </div>

                <!-- Total User -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">Total User</p>
                            <p class="text-4xl font-bold text-purple-400 mt-2"><?php echo $stats['total_user']; ?></p>
                        </div>
                        <span class="text-3xl">👥</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">User reguler aktif</p>
                </div>

                <!-- Pengajuan Pending -->
                <div class="card-stat">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-white/70 text-sm">📋 Pengajuan Pending</p>
                            <p class="text-4xl font-bold text-red-400 mt-2"><?php echo $stats['pengajuan_pending']; ?></p>
                        </div>
                        <span class="text-3xl">📋</span>
                    </div>
                    <p class="text-white/50 text-xs mt-4">Menunggu persetujuan</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Stok Produk Chart -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Top 5 Produk (Stok Terbanyak)</h3>
                    <canvas id="topProductChart"></canvas>
                </div>

                <!-- Stok per Gudang Chart -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Distribusi Stok per Gudang</h3>
                    <canvas id="gudangChart"></canvas>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card p-6 mb-8">
                <h3 class="text-xl font-bold text-white mb-6">Menu Cepat</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    <a href="produk.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">📦</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Produk</p>
                    </a>
                    <a href="kategori.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">🏷️</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Kategori</p>
                    </a>
                    <a href="gudang.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">🏢</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Gudang</p>
                    </a>
                    <a href="stok-masuk.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">📥</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Stok Masuk</p>
                    </a>
                    <a href="stok-keluar.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">📤</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Stok Keluar</p>
                    </a>
                    <a href="transfer.php" class="glass p-4 rounded-xl text-center hover:shadow-lg transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl mb-2">🚚</div>
                        <p class="text-sm font-semibold text-white/80 group-hover:text-white">Transfer</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Topbar -->
    <?php include '../../../includes/topbar.php'; ?>

    <script src="../../../assets/js/main.js"></script>
    <script>
        // Top Products Chart
        <?php
        $top_products = fetchAll($conn, "SELECT nama_produk, stok_total FROM produk ORDER BY stok_total DESC LIMIT 5");
        $product_names = json_encode(array_column($top_products, 'nama_produk'));
        $product_stocks = json_encode(array_column($top_products, 'stok_total'));
        ?>
        
        const topCtx = document.getElementById('topProductChart').getContext('2d');
        new Chart(topCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $product_names; ?>,
                datasets: [{
                    label: 'Stok',
                    data: <?php echo $product_stocks; ?>,
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: 'rgba(255,255,255,0.7)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: 'rgba(255,255,255,0.7)' }
                    }
                }
            }
        });

        // Gudang Distribution Chart
        <?php
        $gudang_data = fetchAll($conn, "SELECT g.nama_gudang, COALESCE(SUM(sg.stok), 0) as total_stok FROM gudang g LEFT JOIN stok_gudang sg ON g.id = sg.gudang_id GROUP BY g.id");
        $gudang_names = json_encode(array_column($gudang_data, 'nama_gudang'));
        $gudang_stocks = json_encode(array_column($gudang_data, 'total_stok'));
        ?>
        
        const gudangCtx = document.getElementById('gudangChart').getContext('2d');
        new Chart(gudangCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo $gudang_names; ?>,
                datasets: [{
                    data: <?php echo $gudang_stocks; ?>,
                    backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                    borderColor: '#1e293b',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: { color: 'rgba(255,255,255,0.7)' }
                    }
                }
            }
        });
    </script>
</body>
</html>