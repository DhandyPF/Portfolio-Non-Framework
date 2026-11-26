<?php

session_start();
require_once __DIR__ . '/functions.php';

// Ambil seluruh data portfolio dari database untuk ditampilkan di halaman utama.
try {
    $data = get_portfolio_data();
    $databaseError = null;
} catch (Throwable $exception) {
    // Halaman tetap dapat dirender ketika database belum tersambung.
    $data = [
        'profile' => [],
        'education' => [],
        'certificates' => [],
        'skill_groups' => [],
        'interests' => [],
    ];
    $databaseError = $exception->getMessage();
}

$profile = $data['profile'];
$flash = get_flash();

// Daftar navigasi dibuat dari array agar tautan mudah ditambah atau diubah.
$navLinks = [
    ['href' => '#tentang', 'label' => 'Tentang'],
    ['href' => '#pendidikan', 'label' => 'Pendidikan'],
    ['href' => '#kegiatan', 'label' => 'Kegiatan'],
    ['href' => '#keahlian', 'label' => 'Keahlian'],
    ['href' => '#peminatan', 'label' => 'Peminatan'],
    ['href' => '#kontak', 'label' => 'Kontak'],
];
$markers = ['code-block' => '</>', 'robot-brain' => 'AI', 'pickaxe' => '◇'];
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
        content="Portfolio Dhandy Putra Fahruddin, pengembang perangkat lunak dan system enthusiast.">
    <meta name="color-scheme" content="light dark">
    <script>
    try {
        const storedTheme = localStorage.getItem("color-scheme");
        const colorSchemeMeta = document.querySelector('meta[name="color-scheme"]');
        if (storedTheme === "light" || storedTheme === "dark") {
            document.documentElement.dataset.theme = storedTheme;
            colorSchemeMeta.content = storedTheme;
        }
    } catch (error) {
        // Gunakan preferensi sistem jika penyimpanan browser tidak tersedia.
    }
    </script>
    <title><?= e($profile['name'] ?? 'Dhandy Putra Fahruddin') ?> | Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <a class="skip-link" href="#konten-utama">Lewati ke konten utama</a>
    <header class="site-header">
        <div class="page-shell nav-shell">
            <div class="brand-actions">
                <a class="brand" href="#beranda" aria-label="Kembali ke beranda">
                    <span class="brand-mark">D.</span>
                    <span>DHANDY</span>
                </a>
                <button class="theme-toggle" type="button" aria-label="Aktifkan mode gelap">
                    Mode gelap
                </button>
            </div>
            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">Menu</button>
            <nav id="primary-nav" class="primary-nav" aria-label="Navigasi utama">
                <?php foreach ($navLinks as $link): ?>
                <a href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>

    <main id="konten-utama">
        <?php if ($databaseError): ?>
        <div class="database-notice page-shell" role="alert">
            Database belum tersambung. <?= e($databaseError) ?>
        </div>
        <?php endif; ?>

        <!-- Hero dan ringkasan portfolio. -->
        <section id="beranda" class="hero section-shell">
            <div class="page-shell hero-grid">
                <div class="reveal">
                    <!-- <p class="eyebrow">Pengembang perangkat lunak / 2026</p> -->
                    <h1>Saya merancang pengalaman digital yang jernih, berguna, dan siap bertumbuh.</h1>
                    <p class="hero-copy">
                        Saya <strong><?= e($profile['name'] ?? 'Dhandy Putra Fahruddin') ?></strong>.
                        Saya membangun produk digital dengan rasa ingin tahu, ketelitian, dan perhatian pada pengalaman
                        manusia.
                    </p>
                    <div class="button-row">
                        <a class="button button-primary" href="#kontak">
                            Mulai percakapan <span aria-hidden="true">↗</span>
                        </a>
                        <a class="button button-secondary" href="#tentang">Lihat pendekatan saya</a>
                    </div>
                    <p class="availability">TERBUKA UNTUK PROYEK YANG MEMBUTUHKAN KETELITIAN DAN RASA INGIN TAHU</p>
                </div>
                <div class="workspace-card reveal reveal-delay-2" aria-label="Ringkasan portofolio">
                    <div class="workspace-bar"><span>DHANDY / WORKSPACE</span></div>
                    <div class="workspace-body">
                        <div class="workspace-heading">
                            <div>
                                <p class="eyebrow">SELECTED WORK</p>
                                <h2>Sistem yang terasa sederhana.</h2>
                            </div>
                            <span class="square-icon" aria-hidden="true">↗</span>
                        </div>
                        <div class="workspace-panels">
                            <div class="wireframe-panel">
                                <span class="dots" aria-hidden="true">
                                    <i></i><i></i><i></i>
                                </span>
                                <div class="wire-lines">
                                    <span></span><span></span><span></span>
                                </div>
                            </div>
                            <div class="focus-panel">
                                <span>FOCUS</span>
                                <strong>03</strong>
                                <small>ideas in motion</small>
                            </div>
                        </div>
                        <div class="tag-row"><span>PRODUCT THINKING</span><span>FRONTEND</span><span>CURIOSITY</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Informasi profil dan cara kerja. -->
        <section id="tentang" class="section-shell section-muted">
            <div class="page-shell">
                <div class="section-heading reveal">
                    <div>
                        <!-- <p class="eyebrow">Tentang saya</p> -->
                        <h2>Dua dunia, <em>satu rasa ingin tahu.</em></h2>
                    </div>
                    <!-- <p>
                            Tentang cara saya belajar, membangun, dan menjaga rasa ingin tahu
                            tetap menjadi bagian dari setiap proses.
                        </p> -->
                </div>
                <div class="about-grid">
                    <figure class="profile-card reveal">
                        <img src="assets/images/profile-3x4.jpg" alt="Foto profil Dhandy Putra Fahruddin">
                        <figcaption>
                            <span><?= e($profile['name'] ?? 'Dhandy Putra Fahruddin') ?></span>
                            <span>Jawa Barat, ID</span>
                        </figcaption>
                    </figure>
                    <div class="about-content reveal reveal-delay-1">
                        <div class="intro-card">
                            <p>
                                <?= e($profile['bio'] ?? 'Saya senang belajar dengan mencoba langsung, merapikan ide yang kompleks, dan membagikan hasilnya dengan cara yang mudah dipahami.') ?>
                            </p>
                        </div>
                        <div class="persona-grid">
                            <article class="card persona-card">
                                <h3>Perancang dan Pembangun</h3>
                                <p>
                                    Minecraft mengajarkan saya untuk merencanakan, bereksperimen,
                                    dan membangun sesuatu secara bertahap.
                                </p>
                                <small>KREATIVITAS · LOGIKA · EKSPLORASI</small>
                            </article>
                            <article class="card persona-card">
                                <h3>Pembaca</h3>
                                <p>
                                    Buku membantu saya memahami perspektif lain dan menemukan wawasan
                                    yang mendorong proses belajar berkelanjutan.
                                </p>
                                <small>RASA INGIN TAHU · WAWASAN · CERITA</small>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Riwayat pendidikan diambil dari tabel education. -->
        <section id="pendidikan" class="section-shell">
            <div class="page-shell">
                <div class="section-heading reveal">
                    <div>
                        <!-- <p class="eyebrow">Riwayat pendidikan</p> -->
                        <h2>Perjalanan akademis.</h2>
                    </div>
                    <!-- <p>Catatan pendidikan dan fokus studi yang membentuk proses belajar saya.</p> -->
                </div>
                <div class="education-grid">
                    <?php foreach ($data['education'] as $item): ?>
                    <article class="card education-card reveal">
                        <img src="assets/images/<?= e($item['logo_file']) ?>?v=20261121"
                            alt="Logo <?= e($item['institusi']) ?>"
                            <?= stripos($item['institusi'], 'Universitas Jenderal Soedirman') !== false ? 'class="logo-universitas-soedirman"' : '' ?>>
                        <div>
                            <h3><?= e($item['institusi']) ?></h3>
                            <p class="muted"><?= e($item['jurusan']) ?></p>
                            <p class="description"><?= e($item['deskripsi']) ?></p>
                            <span class="period"><?= e($item['periode']) ?></span>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Kegiatan dan sertifikat diambil dari tabel certificates. -->
        <section id="kegiatan" class="section-shell section-muted">
            <div class="page-shell">
                <div class="section-heading reveal">
                    <div>
                        <!-- <p class="eyebrow">Kegiatan dan sertifikat</p> -->
                        <h2>Pengalaman yang terus bertumbuh.</h2>
                    </div>
                    <!-- <p>
                            Pelatihan, kegiatan, organisasi, dan pencapaian yang mendukung perjalanan belajar saya.
                        </p> -->
                </div>
                <div class="certificate-grid">
                    <?php foreach ($data['certificates'] as $certificate): ?>
                    <article class="card certificate-card reveal">
                        <img src="assets/images/<?= e($certificate['image_file']) ?>"
                            alt="Preview <?= e($certificate['judul']) ?>">
                        <div>
                            <p class="accent-text"><?= e($certificate['periode']) ?></p>
                            <h3><?= e($certificate['judul']) ?></h3>
                            <p class="muted"><?= e($certificate['penerbit']) ?></p>
                            <p class="description"><?= e($certificate['deskripsi']) ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Kelompok perangkat dan item skill diambil dari database. -->
        <section id="keahlian" class="section-shell">
            <div class="page-shell">
                <div class="section-heading reveal">
                    <div>
                        <!-- <p class="eyebrow">Perangkat kerja</p> -->
                        <h2>Teknologi yang saya gunakan.</h2>
                    </div>
                    <!-- <p>Daftar ini dapat disesuaikan berdasarkan pengalaman dan kebutuhan Anda.</p> -->
                </div>
                <div class="skill-grid">
                    <?php foreach ($data['skill_groups'] as $group): ?>
                    <article class="card skill-card reveal">
                        <h3><?= e($group['judul']) ?></h3>
                        <ul>
                            <?php foreach ($group['items'] as $skill): ?>
                            <li>
                                <img src="assets/images/tech-logos/<?= e($skill['logo_file']) ?>?v=20261121" alt=""
                                    aria-hidden="true">
                                <span><?= e($skill['name']) ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Bidang peminatan diambil dari tabel interests. -->
        <section id="peminatan" class="section-shell section-muted">
            <div class="page-shell">
                <div class="section-heading section-heading-single reveal">
                    <div>
                        <!-- <p class="eyebrow">Bidang peminatan</p> -->
                        <h2>Bidang yang sedang saya tekuni.</h2>
                        <!-- <p>
                                Beberapa topik yang saat ini menarik perhatian saya. Fokus ini dapat
                                berubah seiring hal baru yang saya pelajari.
                            </p> -->
                    </div>
                </div>
                <div class="interest-grid">
                    <?php foreach ($data['interests'] as $index => $interest): ?>
                    <article class="card interest-card reveal">
                        <div class="card-top">
                            <span class="accent-text">
                                <?= e($markers[$interest['ikon']] ?? '•') ?>
                            </span>
                            <span>0<?= $index + 1 ?></span>
                        </div>
                        <h3><?= e($interest['judul']) ?></h3>
                        <p><?= e($interest['deskripsi']) ?></p>
                        <div class="progress" aria-label="Tahap eksplorasi <?= $index + 1 ?> dari 3">
                            <?php for ($step = 0; $step < 3; $step++): ?>
                            <i class="<?= $step <= $index ? 'is-active' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Form kontak dinonaktifkan sementara; email tetap dapat ditampilkan. -->
        <section id="kontak" class="section-shell">
            <div class="page-shell contact-grid">
                <div class="reveal">
                    <!-- <p class="eyebrow">Mari terhubung</p> -->
                    <h2>Ada gagasan yang perlu dibangun?</h2>
                    <p class="contact-copy">Fitur kontak sementara belum tersedia.</p>
                    <p class="email-note">
                        <span>EMAIL</span>
                        <?= e($profile['email'] ?? 'Belum tersedia.') ?>
                    </p>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="page-shell footer-inner">
            <p>© <?= date('Y') ?> Dhandy Putra Fahruddin.</p>
            <div>
                <a href="https://github.com/DhandyPF" target="_blank" rel="noreferrer">
                    GitHub ↗
                </a>
                <a href="#beranda">Kembali ke atas ↑</a>
            </div>
        </div>
    </footer>
    <script src="assets/js/main.js"></script>
</body>

</html>