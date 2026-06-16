<?php
require_once '../config/database.php';
require_once '../config/session.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="text-center">
            <div class="text-9xl font-bold text-red-500 mb-4">403</div>
            <h1 class="text-4xl font-bold text-white mb-2">Akses Ditolak</h1>
            <p class="text-white/70 mb-8 text-lg">
                Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="../index.php" class="btn btn-primary">
                    🏠 Kembali ke Beranda
                </a>
                <a href="../auth/logout.php" class="btn btn-outline">
                    🚪 Logout
                </a>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>