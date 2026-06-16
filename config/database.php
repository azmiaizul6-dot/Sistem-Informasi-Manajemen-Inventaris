<?php
// ========================================
// KONFIGURASI DATABASE
// ========================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventaris_db');

// Buat koneksi ke database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Set charset ke utf8
$conn->set_charset("utf8");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// ========================================
// FUNGSI HELPER
// ========================================

/**
 * Escape string untuk mencegah SQL Injection
 */
function escape($conn, $string) {
    return $conn->real_escape_string($string);
}

/**
 * Execute query
 */
function executeQuery($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query Error: " . $conn->error);
        return false;
    }
    return $result;
}

/**
 * Fetch semua data
 */
function fetchAll($conn, $sql) {
    $result = executeQuery($conn, $sql);
    if (!$result) return [];
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

/**
 * Fetch satu data
 */
function fetchOne($conn, $sql) {
    $result = executeQuery($conn, $sql);
    if (!$result) return null;
    return $result->fetch_assoc();
}

/**
 * Format rupiah
 */
function formatRupiah($value) {
    return "Rp " . number_format($value, 0, ',', '.');
}

/**
 * Format tanggal
 */
function formatTanggal($date, $format = 'd M Y H:i') {
    return date($format, strtotime($date));
}

/**
 * Generate kode unik
 */
function generateKode($prefix, $number) {
    return $prefix . '-' . date('Ymd') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
}

/**
 * Log aktivitas (Audit Trail)
 */
function logAktivitas($conn, $user_id, $aksi, $modul, $data_lama = null, $data_baru = null) {
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $data_lama_json = $data_lama ? json_encode($data_lama) : null;
    $data_baru_json = $data_baru ? json_encode($data_baru) : null;
    
    $sql = "INSERT INTO audit_trail (user_id, aksi, modul, data_lama, data_baru, ip_address, user_agent) 
            VALUES ($user_id, '$aksi', '$modul', '$data_lama_json', '$data_baru_json', '$ip_address', '$user_agent')";
    
    return executeQuery($conn, $sql);
}

/**
 * Buat notifikasi stok minimum
 */
function createNotifikasiStok($conn, $produk_id, $gudang_id = null) {
    $produk = fetchOne($conn, "SELECT stok_total, stok_minimum FROM produk WHERE id = $produk_id");
    
    if ($produk && $produk['stok_total'] <= $produk['stok_minimum']) {
        $sql = "INSERT INTO notifikasi_stok_minimum (produk_id, gudang_id, stok_saat_ini, stok_minimum, status) 
                VALUES ($produk_id, $gudang_id, {$produk['stok_total']}, {$produk['stok_minimum']}, 'aktif')
                ON DUPLICATE KEY UPDATE status = 'aktif'";
        return executeQuery($conn, $sql);
    }
    return true;
}

/**
 * Get statistik dashboard
 */
function getStatistikDashboard($conn) {
    return [
        'total_produk' => fetchOne($conn, "SELECT COUNT(*) as count FROM produk")['count'],
        'total_stok' => fetchOne($conn, "SELECT SUM(stok_total) as total FROM produk")['total'] ?? 0,
        'total_gudang' => fetchOne($conn, "SELECT COUNT(*) as count FROM gudang")['count'],
        'stok_minimum' => fetchOne($conn, "SELECT COUNT(*) as count FROM notifikasi_stok_minimum WHERE status = 'aktif'")['count'],
        'total_user' => fetchOne($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'],
        'pengajuan_pending' => fetchOne($conn, "SELECT COUNT(*) as count FROM pengajuan_peminjaman WHERE status = 'pending'")['count']
    ];
}

?>