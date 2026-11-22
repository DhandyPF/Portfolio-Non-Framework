<?php

return [
    // Sesuaikan koneksi ini dengan konfigurasi MySQL lokal atau server.
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'name' => 'dhandy_portfolio',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'admin' => [
        'username' => 'admin',
        // Ganti hash ini sebelum deploy. Password awal: admin123
        'password_hash' => '$2y$10$LWsLhqBNC3HaONSsKqUKu.10L3KQ4xJ6uJDKJ9Mmv5ZP5VvzSmu8i',
    ],
];
