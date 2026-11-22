<?php

session_start();
require_once __DIR__ . '/functions.php';

// Muat kredensial admin dan status autentikasi dari sesi saat ini.
$config = require __DIR__ . '/config.php';
$adminConfig = $config['admin'] ?? [];
$isLoggedIn = !empty($_SESSION['admin_authenticated']);

function admin_redirect(string $message = '', string $type = 'success', string $anchor = ''): never
{
    if ($message !== '') {
        $_SESSION['admin_flash'] = ['message' => $message, 'type' => $type];
    }

    header('Location: admin.php' . ($anchor !== '' ? '#' . $anchor : ''));
    exit;
}

function admin_require_login(): void
{
    if (empty($_SESSION['admin_authenticated'])) {
        header('Location: admin.php');
        exit;
    }
}

function admin_input(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = admin_input('username');
    $password = (string) ($_POST['password'] ?? '');
    if ($username === ($adminConfig['username'] ?? '') && password_verify($password, (string) ($adminConfig['password_hash'] ?? ''))) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        admin_redirect('Selamat datang di dashboard admin.');
    }
    $_SESSION['admin_login_error'] = 'Nama pengguna atau kata sandi salah.';
    header('Location: admin.php');
    exit;
}

// Logout menghapus status autentikasi dan mengganti ID sesi.
if (($_GET['action'] ?? '') === 'logout') {
    unset($_SESSION['admin_authenticated']);
    session_regenerate_id(true);
    header('Location: admin.php');
    exit;
}

if ($isLoggedIn && $_SERVER['REQUEST_METHOD'] === 'POST') {
    admin_require_login();
    if (!verify_csrf((string) ($_POST['csrf_token'] ?? ''))) {
        admin_redirect('Sesi formulir tidak valid. Muat ulang halaman dan coba lagi.', 'error');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);
    $anchor = (string) ($_POST['anchor'] ?? '');

    // Semua operasi CRUD dashboard dipusatkan di satu switch berdasarkan action form.
    try {
        $pdo = get_database();
        switch ($action) {
            case 'save_profile':
                $name = admin_input('name');
                $email = admin_input('email');
                $bio = admin_input('bio');
                if ($name === '' || $bio === '' || ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))) {
                    admin_redirect('Nama, bio, dan format email harus valid.', 'error', 'tentang');
                }
                $profile = $pdo->query('SELECT id FROM profile LIMIT 1')->fetch();
                if ($profile) {
                    $statement = $pdo->prepare(
                        'UPDATE profile
                         SET name = :name, email = :email, bio = :bio
                         WHERE id = :id'
                    );
                    $statement->execute([
                        ':name' => $name,
                        ':email' => $email ?: null,
                        ':bio' => $bio,
                        ':id' => $profile['id'],
                    ]);
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO profile (name, email, bio)
                         VALUES (:name, :email, :bio)'
                    );
                    $statement->execute([
                        ':name' => $name,
                        ':email' => $email ?: null,
                        ':bio' => $bio,
                    ]);
                }
                admin_redirect('Data tentang saya berhasil disimpan.', 'success', 'tentang');

            case 'save_education':
                $fields = ['institusi', 'logo_file', 'jurusan', 'periode', 'start_year', 'deskripsi'];
                $values = array_combine($fields, array_map('admin_input', $fields));
                if (in_array('', $values, true) || !ctype_digit($values['start_year'])) {
                    admin_redirect('Semua data pendidikan harus diisi dengan benar.', 'error', 'pendidikan');
                }
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE education
                         SET institusi = :institusi, logo_file = :logo_file,
                             jurusan = :jurusan, periode = :periode,
                             start_year = :start_year, deskripsi = :deskripsi
                         WHERE id = :id'
                    );
                    $values['id'] = $id;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO education
                            (institusi, logo_file, jurusan, periode, start_year, deskripsi)
                         VALUES
                            (:institusi, :logo_file, :jurusan, :periode, :start_year, :deskripsi)'
                    );
                }
                $statement->execute($values);
                admin_redirect('Data pendidikan berhasil disimpan.', 'success', 'pendidikan');

            case 'delete_education':
                $statement = $pdo->prepare('DELETE FROM education WHERE id = :id');
                $statement->execute([':id' => $id]);
                admin_redirect('Data pendidikan berhasil dihapus.', 'success', 'pendidikan');

            case 'save_certificate':
                $fields = ['judul', 'penerbit', 'periode', 'image_file', 'deskripsi', 'sort_order'];
                $values = array_combine($fields, array_map('admin_input', $fields));
                if (in_array('', $values, true) || !ctype_digit($values['sort_order'])) {
                    admin_redirect('Semua data kegiatan dan sertifikat harus diisi dengan benar.', 'error', 'kegiatan');
                }
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE certificates
                         SET judul = :judul, penerbit = :penerbit,
                             periode = :periode, image_file = :image_file,
                             deskripsi = :deskripsi, sort_order = :sort_order
                         WHERE id = :id'
                    );
                    $values['id'] = $id;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO certificates
                            (judul, penerbit, periode, image_file, deskripsi, sort_order)
                         VALUES
                            (:judul, :penerbit, :periode, :image_file, :deskripsi, :sort_order)'
                    );
                }
                $statement->execute($values);
                admin_redirect('Kegiatan atau sertifikat berhasil disimpan.', 'success', 'kegiatan');

            case 'delete_certificate':
                $statement = $pdo->prepare('DELETE FROM certificates WHERE id = :id');
                $statement->execute([':id' => $id]);
                admin_redirect('Kegiatan atau sertifikat berhasil dihapus.', 'success', 'kegiatan');

            case 'save_group':
                $fields = ['number', 'judul', 'sort_order'];
                $values = array_combine($fields, array_map('admin_input', $fields));
                if (in_array('', $values, true) || !ctype_digit($values['sort_order'])) {
                    admin_redirect('Nomor, judul, dan urutan grup perangkat harus diisi dengan benar.', 'error', 'perangkat');
                }
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE skill_groups
                         SET number = :number, judul = :judul, sort_order = :sort_order
                         WHERE id = :id'
                    );
                    $values['id'] = $id;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO skill_groups (number, judul, sort_order)
                         VALUES (:number, :judul, :sort_order)'
                    );
                }
                $statement->execute($values);
                admin_redirect('Grup perangkat berhasil disimpan.', 'success', 'perangkat');

            case 'delete_group':
                $statement = $pdo->prepare('DELETE FROM skill_groups WHERE id = :id');
                $statement->execute([':id' => $id]);
                admin_redirect('Grup perangkat dan item di dalamnya berhasil dihapus.', 'success', 'perangkat');

            case 'save_skill':
                $fields = ['group_id', 'name', 'logo_file', 'sort_order'];
                $values = array_combine($fields, array_map('admin_input', $fields));
                if (in_array('', $values, true) || !ctype_digit($values['group_id']) || !ctype_digit($values['sort_order'])) {
                    admin_redirect('Grup, nama, logo, dan urutan perangkat harus diisi dengan benar.', 'error', 'perangkat');
                }
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE skills
                         SET group_id = :group_id, name = :name,
                             logo_file = :logo_file, sort_order = :sort_order
                         WHERE id = :id'
                    );
                    $values['id'] = $id;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO skills (group_id, name, logo_file, sort_order)
                         VALUES (:group_id, :name, :logo_file, :sort_order)'
                    );
                }
                $statement->execute($values);
                admin_redirect('Perangkat kerja berhasil disimpan.', 'success', 'perangkat');

            case 'delete_skill':
                $statement = $pdo->prepare('DELETE FROM skills WHERE id = :id');
                $statement->execute([':id' => $id]);
                admin_redirect('Perangkat kerja berhasil dihapus.', 'success', 'perangkat');

            case 'save_interest':
                $fields = ['judul', 'deskripsi', 'ikon', 'sort_order'];
                $values = array_combine($fields, array_map('admin_input', $fields));
                if (in_array('', $values, true) || !ctype_digit($values['sort_order'])) {
                    admin_redirect('Judul, deskripsi, ikon, dan urutan peminatan harus diisi dengan benar.', 'error', 'peminatan');
                }
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE interests
                         SET judul = :judul, deskripsi = :deskripsi,
                             ikon = :ikon, sort_order = :sort_order
                         WHERE id = :id'
                    );
                    $values['id'] = $id;
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO interests (judul, deskripsi, ikon, sort_order)
                         VALUES (:judul, :deskripsi, :ikon, :sort_order)'
                    );
                }
                $statement->execute($values);
                admin_redirect('Bidang peminatan berhasil disimpan.', 'success', 'peminatan');

            case 'delete_interest':
                $statement = $pdo->prepare('DELETE FROM interests WHERE id = :id');
                $statement->execute([':id' => $id]);
                admin_redirect('Bidang peminatan berhasil dihapus.', 'success', 'peminatan');
        }
    } catch (Throwable $exception) {
        error_log('Gagal mengelola dashboard admin: ' . $exception->getMessage());
        admin_redirect('Perubahan belum berhasil disimpan. Periksa koneksi database dan data yang dikirim.', 'error', $anchor);
    }
}

if (!$isLoggedIn) {
    $loginError = $_SESSION['admin_login_error'] ?? null;
    unset($_SESSION['admin_login_error']);
    ?>
    <!doctype html>
    <html lang="id">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Login Admin | Portfolio</title>
            <link rel="stylesheet" href="assets/css/admin.css">
        </head>
        <body class="admin-page admin-login-page">
            <main class="login-card">
                <p class="eyebrow">DHANDY / ADMIN</p>
                <h1>Kelola portfolio.</h1>
                <p class="muted">Masuk untuk mengelola konten situs tanpa mengubah kode sumber.</p>
                <?php if ($loginError): ?>
                    <p class="alert error" role="alert"><?= e($loginError) ?></p>
                <?php endif; ?>
                <form method="post" class="admin-form">
                    <input type="hidden" name="action" value="login">
                    <label>
                        Nama pengguna
                        <input type="text" name="username" autocomplete="username" required autofocus>
                    </label>
                    <label>
                        Kata sandi
                        <input type="password" name="password" autocomplete="current-password" required>
                    </label>
                    <button class="button button-primary" type="submit">Masuk ke dashboard</button>
                </form>
                <a class="back-link" href="index.php">← Kembali ke portfolio</a>
            </main>
        </body>
    </html>
    <?php
    exit;
}

// Ambil data untuk mengisi daftar dan form edit di dashboard.
$pdo = get_database();
$profile = $pdo->query('SELECT * FROM profile LIMIT 1')->fetch() ?: [];
$education = $pdo->query('SELECT * FROM education ORDER BY start_year ASC, id ASC')->fetchAll();
$certificates = $pdo->query('SELECT * FROM certificates ORDER BY sort_order ASC, id ASC')->fetchAll();
$groups = $pdo->query('SELECT * FROM skill_groups ORDER BY sort_order ASC, id ASC')->fetchAll();
$skills = $pdo->query(
    'SELECT skills.*, skill_groups.judul AS group_title
     FROM skills
     JOIN skill_groups ON skill_groups.id = skills.group_id
     ORDER BY skill_groups.sort_order, skills.sort_order, skills.id'
)->fetchAll();
$interests = $pdo->query('SELECT * FROM interests ORDER BY sort_order ASC, id ASC')->fetchAll();
$flash = $_SESSION['admin_flash'] ?? null;
unset($_SESSION['admin_flash']);
$edit = explode(':', (string) ($_GET['edit'] ?? ''), 2);
$editType = $edit[0] ?? '';
$editId = (int) ($edit[1] ?? 0);
$find = static function (array $items, int $id): array {
    foreach ($items as $item) {
        if ((int) $item['id'] === $id) {
            return $item;
        }
    }
    return [];
};
$educationEdit = $editType === 'education' ? $find($education, $editId) : [];
$certificateEdit = $editType === 'certificate' ? $find($certificates, $editId) : [];
$groupEdit = $editType === 'group' ? $find($groups, $editId) : [];
$skillEdit = $editType === 'skill' ? $find($skills, $editId) : [];
$interestEdit = $editType === 'interest' ? $find($interests, $editId) : [];
?>
<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Admin | Portfolio</title>
        <link rel="stylesheet" href="assets/css/admin.css">
    </head>
    <body class="admin-page">
        <header class="admin-header">
            <div>
                <p class="eyebrow">DHANDY / ADMIN</p>
                <h1>Dashboard konten</h1>
            </div>
            <div class="header-actions">
                <a class="button button-secondary" href="index.php">Lihat portfolio</a>
                <a class="button button-secondary" href="admin.php?action=logout">Keluar</a>
            </div>
        </header>
        <main class="admin-container">
            <?php if ($flash): ?>
                <p class="alert <?= e($flash['type']) ?>" role="status">
                    <?= e($flash['message']) ?>
                </p>
            <?php endif; ?>
            <p class="intro-text">Kelola lima bagian utama portfolio. Kolom path gambar mengacu pada folder <code>assets/images</code>.</p>

            <section id="tentang" class="admin-section">
                <div class="section-title">
                    <p class="eyebrow">Tentang saya</p>
                    <h2>Profil</h2>
                </div>
                <form method="post" class="admin-form panel">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action" value="save_profile">
                    <input type="hidden" name="anchor" value="tentang">
                    <label>
                        Nama
                        <input name="name" value="<?= e($profile['name'] ?? '') ?>" required>
                    </label>
                    <label>
                        Email
                        <input type="email" name="email" value="<?= e($profile['email'] ?? '') ?>">
                    </label>
                    <label>
                        Bio
                        <textarea name="bio" rows="5" required><?= e($profile['bio'] ?? '') ?></textarea>
                    </label>
                    <button class="button button-primary" type="submit">Simpan profil</button>
                </form>
            </section>

            <section id="pendidikan" class="admin-section">
                <div class="section-title">
                    <p class="eyebrow">Riwayat pendidikan</p>
                    <h2>Pendidikan</h2>
                </div>
                <div class="admin-grid">
                    <form method="post" class="admin-form panel">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save_education">
                        <input type="hidden" name="id" value="<?= $educationEdit['id'] ?? 0 ?>">
                        <input type="hidden" name="anchor" value="pendidikan">
                        <h3><?= $educationEdit ? 'Edit pendidikan' : 'Tambah pendidikan' ?></h3>
                        <label>
                            Institusi
                            <input name="institusi" value="<?= e($educationEdit['institusi'] ?? '') ?>" required>
                        </label>
                        <label>
                            Path logo
                            <input
                                name="logo_file"
                                placeholder="edu-logos/nama.png"
                                value="<?= e($educationEdit['logo_file'] ?? '') ?>"
                                required
                            >
                        </label>
                        <label>
                            Jurusan
                            <input name="jurusan" value="<?= e($educationEdit['jurusan'] ?? '') ?>" required>
                        </label>
                        <label>
                            Periode
                            <input name="periode" value="<?= e($educationEdit['periode'] ?? '') ?>" required>
                        </label>
                        <label>
                            Tahun mulai
                            <input
                                type="number"
                                name="start_year"
                                min="1900"
                                max="2200"
                                value="<?= e($educationEdit['start_year'] ?? '') ?>"
                                required
                            >
                        </label>
                        <label>
                            Deskripsi
                            <textarea name="deskripsi" rows="4" required><?= e($educationEdit['deskripsi'] ?? '') ?></textarea>
                        </label>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Simpan</button>
                            <?php if ($educationEdit): ?>
                                <a class="button button-secondary" href="admin.php#pendidikan">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="record-list">
                        <?php foreach ($education as $item): ?>
                            <article class="record">
                                <div>
                                    <strong><?= e($item['institusi']) ?></strong>
                                    <span><?= e($item['jurusan']) ?> · <?= e($item['periode']) ?></span>
                                </div>
                                <div class="record-actions">
                                    <a href="admin.php?edit=education:<?= $item['id'] ?>#pendidikan">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus data pendidikan ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_education">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="anchor" value="pendidikan">
                                        <button class="link-button danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section id="kegiatan" class="admin-section">
                <div class="section-title">
                    <p class="eyebrow">Kegiatan dan sertifikat</p>
                    <h2>Kegiatan</h2>
                </div>
                <div class="admin-grid">
                    <form method="post" class="admin-form panel">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save_certificate">
                        <input type="hidden" name="id" value="<?= $certificateEdit['id'] ?? 0 ?>">
                        <input type="hidden" name="anchor" value="kegiatan">
                        <h3><?= $certificateEdit ? 'Edit kegiatan' : 'Tambah kegiatan' ?></h3>
                        <label>
                            Judul
                            <input name="judul" value="<?= e($certificateEdit['judul'] ?? '') ?>" required>
                        </label>
                        <label>
                            Penerbit / organisasi
                            <input name="penerbit" value="<?= e($certificateEdit['penerbit'] ?? '') ?>" required>
                        </label>
                        <label>
                            Periode
                            <input name="periode" value="<?= e($certificateEdit['periode'] ?? '') ?>" required>
                        </label>
                        <label>
                            Path gambar
                            <input
                                name="image_file"
                                placeholder="certificates/nama.svg"
                                value="<?= e($certificateEdit['image_file'] ?? '') ?>"
                                required
                            >
                        </label>
                        <label>
                            Urutan
                            <input type="number" name="sort_order" min="0" value="<?= e($certificateEdit['sort_order'] ?? 0) ?>" required>
                        </label>
                        <label>
                            Deskripsi
                            <textarea name="deskripsi" rows="4" required><?= e($certificateEdit['deskripsi'] ?? '') ?></textarea>
                        </label>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Simpan</button>
                            <?php if ($certificateEdit): ?>
                                <a class="button button-secondary" href="admin.php#kegiatan">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="record-list">
                        <?php foreach ($certificates as $item): ?>
                            <article class="record">
                                <div>
                                    <strong><?= e($item['judul']) ?></strong>
                                    <span><?= e($item['penerbit']) ?> · <?= e($item['periode']) ?></span>
                                </div>
                                <div class="record-actions">
                                    <a href="admin.php?edit=certificate:<?= $item['id'] ?>#kegiatan">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus kegiatan ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_certificate">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="anchor" value="kegiatan">
                                        <button class="link-button danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section id="perangkat" class="admin-section">
                <div class="section-title">
                    <p class="eyebrow">Perangkat kerja</p>
                    <h2>Grup dan perangkat</h2>
                </div>
                <div class="admin-grid">
                    <form method="post" class="admin-form panel">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save_group">
                        <input type="hidden" name="id" value="<?= $groupEdit['id'] ?? 0 ?>">
                        <input type="hidden" name="anchor" value="perangkat">
                        <h3><?= $groupEdit ? 'Edit grup' : 'Tambah grup' ?></h3>
                        <label>
                            Nomor
                            <input name="number" value="<?= e($groupEdit['number'] ?? '') ?>" required>
                        </label>
                        <label>
                            Judul grup
                            <input name="judul" value="<?= e($groupEdit['judul'] ?? '') ?>" required>
                        </label>
                        <label>
                            Urutan
                            <input type="number" name="sort_order" min="0" value="<?= e($groupEdit['sort_order'] ?? 0) ?>" required>
                        </label>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Simpan grup</button>
                            <?php if ($groupEdit): ?>
                                <a class="button button-secondary" href="admin.php#perangkat">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="record-list">
                        <?php foreach ($groups as $item): ?>
                            <article class="record">
                                <div>
                                    <strong><?= e($item['number']) ?> · <?= e($item['judul']) ?></strong>
                                    <span>Grup perangkat</span>
                                </div>
                                <div class="record-actions">
                                    <a href="admin.php?edit=group:<?= $item['id'] ?>#perangkat">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus grup dan semua perangkat di dalamnya?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_group">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="anchor" value="perangkat">
                                        <button class="link-button danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="admin-grid">
                    <form method="post" class="admin-form panel">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save_skill">
                        <input type="hidden" name="id" value="<?= $skillEdit['id'] ?? 0 ?>">
                        <input type="hidden" name="anchor" value="perangkat">
                        <h3><?= $skillEdit ? 'Edit perangkat' : 'Tambah perangkat' ?></h3>
                        <label>
                            Grup
                            <select name="group_id" required>
                                <option value="">Pilih grup</option>
                                <?php foreach ($groups as $group): ?>
                                    <option
                                        value="<?= $group['id'] ?>"
                                        <?= (string) ($skillEdit['group_id'] ?? '') === (string) $group['id'] ? 'selected' : '' ?>
                                    >
                                        <?= e($group['number'] . ' · ' . $group['judul']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            Nama perangkat
                            <input name="name" value="<?= e($skillEdit['name'] ?? '') ?>" required>
                        </label>
                        <label>
                            Path logo
                            <input
                                name="logo_file"
                                placeholder="tech-logos/logo.svg"
                                value="<?= e($skillEdit['logo_file'] ?? '') ?>"
                                required
                            >
                        </label>
                        <label>
                            Urutan
                            <input type="number" name="sort_order" min="0" value="<?= e($skillEdit['sort_order'] ?? 0) ?>" required>
                        </label>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Simpan perangkat</button>
                            <?php if ($skillEdit): ?>
                                <a class="button button-secondary" href="admin.php#perangkat">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="record-list">
                        <?php foreach ($skills as $item): ?>
                            <article class="record">
                                <div>
                                    <strong><?= e($item['name']) ?></strong>
                                    <span><?= e($item['group_title']) ?></span>
                                </div>
                                <div class="record-actions">
                                    <a href="admin.php?edit=skill:<?= $item['id'] ?>#perangkat">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus perangkat ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_skill">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="anchor" value="perangkat">
                                        <button class="link-button danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section id="peminatan" class="admin-section">
                <div class="section-title">
                    <p class="eyebrow">Bidang peminatan</p>
                    <h2>Peminatan</h2>
                </div>
                <div class="admin-grid">
                    <form method="post" class="admin-form panel">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="action" value="save_interest">
                        <input type="hidden" name="id" value="<?= $interestEdit['id'] ?? 0 ?>">
                        <input type="hidden" name="anchor" value="peminatan">
                        <h3><?= $interestEdit ? 'Edit peminatan' : 'Tambah peminatan' ?></h3>
                        <label>
                            Judul
                            <input name="judul" value="<?= e($interestEdit['judul'] ?? '') ?>" required>
                        </label>
                        <label>
                            Deskripsi
                            <textarea name="deskripsi" rows="4" required><?= e($interestEdit['deskripsi'] ?? '') ?></textarea>
                        </label>
                        <label>
                            Ikon
                            <input name="ikon" value="<?= e($interestEdit['ikon'] ?? '') ?>" placeholder="code-block" required>
                        </label>
                        <label>
                            Urutan
                            <input type="number" name="sort_order" min="0" value="<?= e($interestEdit['sort_order'] ?? 0) ?>" required>
                        </label>
                        <div class="form-actions">
                            <button class="button button-primary" type="submit">Simpan peminatan</button>
                            <?php if ($interestEdit): ?>
                                <a class="button button-secondary" href="admin.php#peminatan">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                    <div class="record-list">
                        <?php foreach ($interests as $item): ?>
                            <article class="record">
                                <div>
                                    <strong><?= e($item['judul']) ?></strong>
                                    <span><?= e($item['ikon']) ?> · urutan <?= e((string) $item['sort_order']) ?></span>
                                </div>
                                <div class="record-actions">
                                    <a href="admin.php?edit=interest:<?= $item['id'] ?>#peminatan">Edit</a>
                                    <form method="post" onsubmit="return confirm('Hapus bidang peminatan ini?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="action" value="delete_interest">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <input type="hidden" name="anchor" value="peminatan">
                                        <button class="link-button danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
