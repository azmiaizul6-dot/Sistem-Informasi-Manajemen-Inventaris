<?php
// ========================================
// KONFIGURASI SESSION
// ========================================

session_start();

// Konfigurasi session
$session_timeout = 3600; // 1 jam dalam detik

// Cek jika session telah timeout
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $session_timeout) {
        session_destroy();
        unset($_SESSION);
        header("Location: ../auth/login.php?timeout=1");
        exit;
    }
}

// Update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();

/**
 * Check apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Check apakah user adalah admin
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Check apakah user adalah regular user
 */
function isUser() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

/**
 * Get user data dari session
 */
function getUserData() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'nama_lengkap' => $_SESSION['nama_lengkap'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}

/**
 * Redirect jika tidak login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../auth/login.php");
        exit;
    }
}

/**
 * Redirect jika bukan admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../pages/unauthorized.php");
        exit;
    }
}

/**
 * Logout user
 */
function logout() {
    session_destroy();
    unset($_SESSION);
    header("Location: ../auth/login.php");
    exit;
}

?>