<?php
// Landing Page - Sistem Manajemen Inventaris
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Inventaris - Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/tailwind.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="topbar fixed top-0 left-0 right-0">
        <div class="max-w-7xl mx-auto w-full flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white font-bold">
                    📦
                </div>
                <span class="text-white font-bold text-xl">Inventaris</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="auth/login.php" class="btn btn-primary btn-sm">Login</a>
                <a href="auth/register.php" class="btn btn-outline btn-sm">Register</a>
                <button onclick="toggleDarkMode()" class="text-white/70 hover:text-white transition-colors">
                    🌙
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="pt-32 pb-20 px-4">
        <div class="max-w-4xl mx-auto text-center animate-fade-in">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                Sistem Manajemen <span class="bg-gradient-to-r from-blue-400 to-emerald-400 bg-clip-text text-transparent">Inventaris</span> Modern
            </h1>
            <p class="text-xl text-white/70 mb-8 leading-relaxed">
                Kelola stok barang, gudang, dan peminjaman dengan mudah dan profesional. 
                Dengan antarmuka yang indah dan fitur-fitur lengkap untuk mendukung bisnis Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="auth/login.php" class="btn btn-primary btn-lg">
                    🚀 Mulai Sekarang
                </a>
                <a href="#features" class="btn btn-outline btn-lg">
                    📖 Pelajari Lebih Lanjut
                </a>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-4xl font-bold text-center text-white mb-16">Fitur Unggulan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📊</div>
                    <h3 class="text-xl font-bold text-white mb-3">Dashboard Analytics</h3>
                    <p class="text-white/70">
                        Pantau stok, gudang, dan pergerakan barang dalam satu dashboard yang komprehensif.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📦</div>
                    <h3 class="text-xl font-bold text-white mb-3">Manajemen Produk</h3>
                    <p class="text-white/70">
                        CRUD lengkap untuk produk, kategori, harga, dan stok dengan mudah.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🏭</div>
                    <h3 class="text-xl font-bold text-white mb-3">Multi Gudang</h3>
                    <p class="text-white/70">
                        Kelola beberapa gudang dengan tracking stok per lokasi secara real-time.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">📤📥</div>
                    <h3 class="text-xl font-bold text-white mb-3">Stok Masuk & Keluar</h3>
                    <p class="text-white/70">
                        Pencatatan otomatis pergerakan barang dengan nomor referensi dan keterangan.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🚚</div>
                    <h3 class="text-xl font-bold text-white mb-3">Transfer Stok</h3>
                    <p class="text-white/70">
                        Transfer barang antar gudang dengan tracking status perjalanan.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="card-glass p-6 hover:shadow-2xl transition-all duration-300 group cursor-pointer">
                    <div class="text-4xl mb-4 group-hover:scale-110 transition-transform">🛡️</div>
                    <h3 class="text-xl font-bold text-white mb-3">Keamanan Tinggi</h3>
                    <p class="text-white/70">
                        Session management, audit trail, dan role-based access control.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="card-stat text-center">
                    <div class="text-4xl font-bold text-blue-400 mb-2">8+</div>
                    <p class="text-white/70">Fitur Utama</p>
                </div>
                <div class="card-stat text-center">
                    <div class="text-4xl font-bold text-emerald-400 mb-2">∞</div>
                    <p class="text-white/70">Skalabilitas</p>
                </div>
                <div class="card-stat text-center">
                    <div class="text-4xl font-bold text-cyan-400 mb-2">100%</div>
                    <p class="text-white/70">Responsive</p>
                </div>
                <div class="card-stat text-center">
                    <div class="text-4xl font-bold text-amber-400 mb-2">24/7</div>
                    <p class="text-white/70">Reliable</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/10 py-12 px-4">
        <div class="max-w-6xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h4 class="font-bold text-white mb-4">Tentang</h4>
                    <p class="text-white/70 text-sm">
                        Sistem Manajemen Inventaris profesional untuk membantu bisnis Anda mengelola stok dengan efisien.
                    </p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Fitur</h4>
                    <ul class="space-y-2 text-white/70 text-sm">
                        <li><a href="#" class="hover:text-white transition-colors">Dashboard</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Manajemen Produk</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Laporan</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Kontak</h4>
                    <p class="text-white/70 text-sm">Email: info@inventaris.com</p>
                    <p class="text-white/70 text-sm">Telepon: +62 (0)21 1234567</p>
                </div>
            </div>
            <div class="border-t border-white/10 pt-8 text-center text-white/50 text-sm">
                <p>&copy; 2024 Sistem Manajemen Inventaris. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>