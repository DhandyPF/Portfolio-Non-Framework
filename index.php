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
$contactEmail = filter_var((string) ($profile['email'] ?? ''), FILTER_VALIDATE_EMAIL) ?: '';

// Daftar navigasi dibuat dari array agar tautan mudah ditambah atau diubah.
$navLinks = [
    ['href' => '#tentang', 'label' => 'Tentang'],
    ['href' => '#pendidikan', 'label' => 'Pendidikan'],
    ['href' => '#proyek', 'label' => 'Proyek'],
    ['href' => '#kegiatan', 'label' => 'Kegiatan'],
    ['href' => '#keahlian', 'label' => 'Keahlian'],
    ['href' => '#peminatan', 'label' => 'Peminatan'],
    ['href' => '#kontak', 'label' => 'Kontak'],
];
$markers = ['code-block' => '</>', 'robot-brain' => 'AI', 'pickaxe' => '◇'];
$projects = [
    [
        'name' => 'TaskFlow',
        'repository' => 'TaskFlow-App',
        'description' => 'Aplikasi manajemen tugas berbasis web dengan akun pengguna, prioritas, tenggat waktu, dan status penyelesaian.',
        'stack' => ['Go', 'Web app', 'Task management'],
    ],
    [
        'name' => 'Sistem Parkir',
        'repository' => 'project-pbo-sistem-parkir',
        'description' => 'Aplikasi desktop Java Swing untuk pengelolaan parkir, dengan penerapan OOP, Strategy Pattern, dan Repository Pattern.',
        'stack' => ['Java', 'Swing', 'OOP'],
    ],
    [
        'name' => 'Website Portfolio',
        'repository' => 'Website-Portfolio',
        'description' => 'Website portfolio pribadi dengan Next.js, React, dan Tailwind CSS.',
        'stack' => ['Next.js', 'React', 'Tailwind CSS'],
    ],
    [
        'name' => 'Metafor 2026',
        'repository' => 'metafor-2026',
        'description' => 'Contoh website profil kelompok sederhana untuk kegiatan Metafor 2026.',
        'stack' => ['HTML', 'Website profil'],
    ],
];

// Tautan sosial media utama pada bagian kontak.
$socialLinks = [
    [
        'platform' => 'Instagram',
        'handle' => '@dhandyputra.f',
        'description' => 'DM atau lihat aktivitas terbaru.',
        'url' => 'https://instagram.com/dhandyputra.f',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>',
    ],
    [
        'platform' => 'Discord',
        'handle' => 'moisdurdane',
        'description' => 'Diskusi cepat atau kolaborasi teknis.',
        'url' => 'https://discord.com/users/moisdurdane',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 14.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm7 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path d="M5.5 6.5A16 16 0 0 1 9 5.5l.6 1.2a12 12 0 0 1 4.8 0L15 5.5a16 16 0 0 1 3.5 1c1.8 2.8 2.3 5.6 2 8.5a15 15 0 0 1-4.5 2.5l-.9-1.4a9.5 9.5 0 0 1-6.2 0L8 17.5A15 15 0 0 1 3.5 15c-.3-2.9.2-5.7 2-8.5Z"/></svg>',
    ],
    [
        'platform' => 'WhatsApp',
        'handle' => '+62 8xx-xxxx-xxxx',
        'description' => 'Chat langsung untuk komunikasi cepat.',
        'url' => 'https://wa.me/628xxxxxxxxxx',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21Z"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/></svg>',
    ],
    [
        'platform' => 'Telegram',
        'handle' => '@username_telegram',
        'description' => 'Kirim pesan langsung melalui Telegram.',
        'url' => 'https://t.me/username_telegram',
        'icon' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.5 3.5-19 7.5 6.5 2.5 9-6-6.5 7.5 5 4 4.5-15.5Z"/></svg>',
    ],
];
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
    <script>
    document.documentElement.classList.add("js");
    </script>
    <title><?= e($profile['name'] ?? 'Dhandy Putra Fahruddin') ?> | Portfolio</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
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
                <article class="workspace-card featured-project reveal reveal-delay-2">
                    <div class="workspace-bar"><span>PROYEK / OPEN SOURCE</span></div>
                    <div class="workspace-body">
                        <p class="eyebrow">PORTFOLIO PERSONAL</p>
                        <h2>Portfolio Non-Framework</h2>
                        <p class="description">Website portfolio ini dibuat dengan PHP, MySQL, HTML, CSS, dan JavaScript.</p>
                        <a class="project-link" href="https://github.com/DhandyPF/Portfolio-Non-Framework"
                            target="_blank" rel="noopener noreferrer">
                            Lihat repositori <span aria-hidden="true">↗</span>
                        </a>
                        <div class="project-tags" aria-label="Teknologi proyek">
                            <span>PHP</span><span>MySQL</span><span>HTML · CSS · JS</span>
                        </div>
                    </div>
                </article>
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

        <section id="proyek" class="section-shell section-muted">
            <div class="page-shell">
                <div class="section-heading">
                    <div>
                        <p class="eyebrow">Repositori publik</p>
                        <h2>Proyek yang bisa ditinjau.</h2>
                    </div>
                    <p>Beberapa proyek yang sudah saya kerjakan. Setiap kartu menuju repositori sumbernya di GitHub.</p>
                </div>
                <div class="project-grid">
                    <?php foreach ($projects as $project): ?>
                    <article class="card project-card">
                        <div class="project-top">
                            <span>PROYEK</span>
                            <span aria-hidden="true">↗</span>
                        </div>
                        <h3>
                            <a href="https://github.com/DhandyPF/<?= e($project['repository']) ?>"
                                target="_blank" rel="noopener noreferrer">
                                <?= e($project['name']) ?>
                            </a>
                        </h3>
                        <p><?= e($project['description']) ?></p>
                        <div class="project-tags" aria-label="Teknologi proyek">
                            <?php foreach ($project['stack'] as $technology): ?>
                            <span><?= e($technology) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a class="project-link" href="https://github.com/DhandyPF/<?= e($project['repository']) ?>"
                            target="_blank" rel="noopener noreferrer"
                            aria-label="Lihat proyek <?= e($project['name']) ?> di GitHub">
                            Lihat di GitHub <span aria-hidden="true">↗</span>
                        </a>
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
                <div class="certificate-carousel" role="region" aria-roledescription="carousel"
                    aria-label="Kegiatan dan sertifikat">
                    <div class="certificate-controls" hidden>
                        <button class="button button-secondary" id="certificate-toggle" type="button"
                            aria-pressed="false">Jeda sertifikat</button>
                    </div>
                    <div class="certificate-grid" id="certificate-track" tabindex="0" role="group"
                        aria-label="Daftar kegiatan dan sertifikat">
                        <?php foreach ($data['certificates'] as $index => $certificate): ?>
                        <article class="card certificate-card" role="group" aria-roledescription="slide"
                            aria-label="<?= $index + 1 ?> dari <?= count($data['certificates']) ?>">
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

        <section id="kontak" class="section-shell">
            <div class="page-shell contact-grid">
                <div class="reveal">
                    <p class="eyebrow">Mari terhubung</p>
                    <h2>Ada gagasan yang perlu dibangun?</h2>
                    <p class="contact-copy">Hubungi saya langsung melalui salah satu akun sosial media di bawah ini untuk diskusi proyek, kolaborasi, atau sekadar menyapa.</p>
                    <p class="email-note">
                        <span>EMAIL</span>
                        <?php if ($contactEmail): ?>
                        <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>
                        <?php else: ?>
                        Belum tersedia.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="social-grid reveal reveal-delay-1">
                    <?php foreach ($socialLinks as $social): ?>
                    <a class="social-card" href="<?= e($social['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <div class="social-card-top">
                            <span class="social-icon"><?= $social['icon'] ?></span>
                            <span class="social-arrow" aria-hidden="true">↗</span>
                        </div>
                        <h3 class="social-platform"><?= e($social['platform']) ?></h3>
                        <p class="social-handle"><?= e($social['handle']) ?></p>
                        <p class="social-desc"><?= e($social['description']) ?></p>
                    </a>
                    <?php endforeach; ?>
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
    <script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/assets/js/main.js') ?>"></script>
</body>

</html>