<?php

require_once __DIR__ . '/db.php';

function get_portfolio_data(): array
{
    $pdo = get_database();

    // Semua data halaman utama dikumpulkan dalam satu struktur.
    return [
        'profile' => $pdo->query('SELECT * FROM profile LIMIT 1')->fetch() ?: [],
        'education' => $pdo->query('SELECT * FROM education ORDER BY start_year ASC, id ASC')->fetchAll(),
        'certificates' => $pdo->query('SELECT * FROM certificates ORDER BY sort_order ASC, id ASC')->fetchAll(),
        'skill_groups' => load_skill_groups($pdo),
        'interests' => $pdo->query('SELECT * FROM interests ORDER BY sort_order ASC, id ASC')->fetchAll(),
    ];
}

function load_skill_groups(PDO $pdo): array
{
    $groups = $pdo->query('SELECT * FROM skill_groups ORDER BY sort_order ASC, id ASC')->fetchAll();
    $items = $pdo->query('SELECT * FROM skills ORDER BY group_id ASC, sort_order ASC, id ASC')->fetchAll();

    // Gabungkan setiap skill ke grupnya agar mudah dirender di halaman utama.
    foreach ($groups as &$group) {
        $group['items'] = array_values(array_filter(
            $items,
            static fn (array $item): bool => (int) $item['group_id'] === (int) $group['id']
        ));
    }

    return $groups;
}

function redirect_with_flash(string $type, string $message): never
{
    // Simpan notifikasi sebelum redirect agar tampil pada request berikutnya.
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    header('Location: index.php#kontak');
    exit;
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    // Token dipakai untuk memastikan request form berasal dari sesi yang valid.
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
