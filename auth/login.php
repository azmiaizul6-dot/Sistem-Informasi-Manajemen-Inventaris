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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($conn, $_POST['username']);
    $password = escape($conn, $_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = "Username dan password harus diisi!";
    } else {
        // Query user
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND status = 'aktif'";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Log aktivitas
            logAktivitas($conn, $user['id'], 'LOGIN', 'Auth');
            
            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: ../pages/admin/dashboard.php");
            } else {
                header("Location: ../pages/user/dashboard.php");
            }
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    }
}

$timeout = isset($_GET['timeout']) ? true : false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/tailwind.css">
</head>
<body>
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-4">
                    <span class="text-3xl">📦</span>
                </div>
                <h1 class="text-3xl font-bold text-white">Inventaris</h1>
                <p class="text-white/70 mt-2">Manajemen Stok Profesional</p>
            </div>

            <!-- Alert -->
            <div id="alert-container"></div>

            <?php if ($timeout): ?>
                <div class="alert alert-warning mb-4">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-message">Sesi Anda telah expired. Silakan login kembali.</div>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4">
                    <div class="alert-icon">✕</div>
                    <div class="alert-message"><?php echo $error; ?></div>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <div class="card p-8">
                <form method="POST" onsubmit="return validateForm('loginForm')">
                    <input type="hidden" id="loginForm">
                    
                    <!-- Username -->
                    <div class="form-group">
                        <label class="label-base">Username</label>
                        <input type="text" name="username" placeholder="Masukkan username" class="input-base" required>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="label-base">Password</label>
                        <input type="password" name="password" placeholder="Masukkan password" class="input-base" required>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2 mb-6">
                        <input type="checkbox" id="remember" class="w-4 h-4">
                        <label for="remember" class="text-sm text-white/70">Ingat saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-full mb-4">
                        Login
                    </button>
                </form>

                <!-- Demo Credentials -->
                <div class="border-t border-white/10 pt-4">
                    <p class="text-xs text-white/50 mb-2">Demo Credentials:</p>
                    <p class="text-xs text-white/70 mb-1"><strong>Admin:</strong> admin / admin123</p>
                    <p class="text-xs text-white/70"><strong>User:</strong> user1 / user123</p>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center mt-6">
                <p class="text-white/70">
                    Belum punya akun? 
                    <a href="register.php" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors">
                        Daftar sekarang
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>