<?php
require_once '../config/database.php';
require_once '../config/session.php';

requireLogin();

// Log aktivitas logout
if (isset($_SESSION['user_id'])) {
    logAktivitas($conn, $_SESSION['user_id'], 'LOGOUT', 'Auth');
}

// Logout
logout();
?>