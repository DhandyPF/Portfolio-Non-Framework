<?php

session_start();
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php#kontak');
    exit;
}

$fields = ['name', 'email', 'subject', 'message'];
$values = [];
foreach ($fields as $field) {
    $value = $_POST[$field] ?? '';
    $values[$field] = is_string($value) ? trim($value) : '';
}

$length = static function (string $value): int {
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
};

$errors = [];
$csrfToken = $_POST['csrf_token'] ?? '';
if (!is_string($csrfToken) || !verify_csrf($csrfToken)) {
    $errors[] = 'Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.';
}
if (in_array('', $values, true)) {
    $errors[] = 'Semua kolom wajib diisi.';
}
if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Masukkan alamat email yang valid.';
}
if (
    $length($values['name']) > 150
    || $length($values['email']) > 190
    || $length($values['subject']) > 190
    || $length($values['message']) > 5000
) {
    $errors[] = 'Ada kolom yang melebihi batas panjang.';
}

if ($errors) {
    $_SESSION['contact_form'] = $values;
    redirect_with_flash('error', implode(' ', $errors));
}

try {
    $statement = get_database()->prepare(
        'INSERT INTO messages (name, email, subject, message)
         VALUES (:name, :email, :subject, :message)'
    );
    $statement->execute([
        ':name' => $values['name'],
        ':email' => $values['email'],
        ':subject' => $values['subject'],
        ':message' => $values['message'],
    ]);
} catch (RuntimeException $exception) {
    error_log('Unable to save contact message: ' . $exception->getMessage());
    $_SESSION['contact_form'] = $values;
    redirect_with_flash('error', 'Pesan belum dapat disimpan. Silakan coba lagi atau kirim email langsung.');
}

unset($_SESSION['contact_form']);
redirect_with_flash('success', 'Terima kasih. Pesan Anda sudah saya terima.');
