<?php
/**
 * Index Page - Koperasi Pegawai RSUD Tarakan
 * Redirects to login page or dashboard based on authentication status
 */

session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // User is logged in, redirect to dashboard
    header('Location: dashboard.php');
    exit;
} else {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit;
}
?>
