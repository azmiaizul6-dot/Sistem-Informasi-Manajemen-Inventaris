<?php
require_once '../config/database.php';
require_once '../config/session.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: ../pages/admin/dashboard.php");
    } else {
        header("Location: ../pages/user/dashboard.php");
    }
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($conn, $_POST['username']);
    $email = escape($conn, $_POST['email']);
    $password = escape($conn, $_POST['password']);
    $password_confirm = escape($conn, $_POST['password_confirm']);
    $nama_lengkap = escape($conn, $_POST['nama_lengkap']);
    $no_telepon = escape($conn, $_POST['no_telepon']);
    
    // Validasi
    if (empty($username) || empty($email) || empty($password) || empty($nama_lengkap)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $password_confirm) {
        $error = "Password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Cek apakah username sudah ada
        $check_username = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check_username->num_rows > 0) {
            $error = "Username sudah terdaftar!";
        } else {
            // Cek apakah email sudah ada
            $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
            if ($check_email->num_rows > 0) {
                $error = "Email sudah terdaftar!";
            } else {
                // Insert user baru
                $sql = "INSERT INTO users (username, password, email, nama_lengkap, no_telepon, role, status) 
                        VALUES ('$username', '$password', '$email', '$nama_lengkap', '$no_telepon', 'user', 'aktif')";
                
                if ($conn->query($sql)) {
                    $success = "Registrasi berhasil! Silakan login dengan akun baru Anda.";
                } else {
                    $error = "Terjadi kesalahan saat registrasi: " . $conn->error;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-4">
                    <span class="text-3xl">📦</span>
                </div>
                <h1 class="text-3xl font-bold text-white">Inventaris</h1>
                <p class="text-white/70 mt-2">Daftar Akun Baru</p>
            </div>

            <!-- Alert -->
            <div id="alert-container"></div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4">
                    <div class="alert-icon">✕</div>
                    <div class="alert-message"><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success mb-4">
                    <div class="alert-icon">✓</div>
                    <div class="alert-message"><?php echo $success; ?></div>
                </div>
                <div class="text-center">
                    <a href="login.php" class="btn btn-primary">Ke Halaman Login</a>
                </div>
            <?php else: ?>

            <!-- Form Register -->
            <div class="card p-8">
                <form method="POST" onsubmit="return validateForm('registerForm')">
                    <input type="hidden" id="registerForm">
                    
                    <!-- Nama Lengkap -->
                    <div class="form-group">
                        <label class="label-base">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap" class="input-base" required>
                    </div>

                    <!-- Username -->
                    <div class="form-group">
                        <label class="label-base">Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" class="input-base" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label class="label-base">Email</label>
                        <input type="email" name="email" placeholder="Masukkan email" class="input-base" required>
                    </div>

                    <!-- No Telepon -->
                    <div class="form-group">
                        <label class="label-base">No Telepon</label>
                        <input type="tel" name="no_telepon" placeholder="Masukkan nomor telepon" class="input-base">
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="label-base">Password</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" class="input-base" required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="label-base">Konfirmasi Password</label>
                        <input type="password" name="password_confirm" placeholder="Ulangi password" class="input-base" required>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="flex items-start gap-2 mb-6">
                        <input type="checkbox" id="terms" class="w-4 h-4 mt-1" required>
                        <label for="terms" class="text-sm text-white/70">
                            Saya setuju dengan <a href="#" class="text-blue-400 hover:text-blue-300">Syarat & Ketentuan</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full mb-4">
                        Daftar
                    </button>
                </form>
            </div>

            <!-- Login Link -->
            <div class="text-center mt-6">
                <p class="text-white/70">
                    Sudah punya akun? 
                    <a href="login.php" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                        Login sekarang
                    </a>
                </p>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>