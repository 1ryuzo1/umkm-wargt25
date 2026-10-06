<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email_pelapor'] ?? '');
    $deskripsi = trim($_POST['deskripsi_bug'] ?? '');

    if (filter_var($email, FILTER_VALIDATE_EMAIL) && $deskripsi !== '') {
        $baris = sprintf(
            "[%s] %s: %s\n",
            date('Y-m-d H:i:s'),
            $email,
            str_replace(["\r", "\n"], ' ', $deskripsi)
        );
        // Simpan laporan ke file log sederhana (belum ada tabel khusus di database untuk ini)
        @file_put_contents(__DIR__ . '/laporan_bug.log', $baris, FILE_APPEND);
    }

    $redirect = 'index.php';
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $ref = $referer ? parse_url($referer) : false;
    $currentHost = $_SERVER['HTTP_HOST'] ?? '';
    if (is_array($ref) && !empty($ref['host']) && $currentHost !== '' && strcasecmp($ref['host'], $currentHost) === 0) {
        $redirect = $referer;
    }
    echo "<script>alert('Terima kasih! Laporan Anda sudah kami terima.'); window.location.href=" . json_encode($redirect) . ";</script>";
    exit;
}

header("Location: index.php");
exit;
