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
$contactForm = $_SESSION['contact_form'] ?? [];
unset($_SESSION['contact_form']);
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
                    <p class="contact-copy">Kirim pesan melalui formulir ini. Pesan akan tersimpan dan bisa saya tindak lanjuti melalui email.</p>
                    <p class="email-note">
                        <span>EMAIL</span>
                        <?php if ($contactEmail): ?>
                        <a href="mailto:<?= e($contactEmail) ?>"><?= e($contactEmail) ?></a>
                        <?php else: ?>
                        Belum tersedia.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="reveal reveal-delay-1">
                    <?php if ($flash): ?>
                    <p class="alert <?= e($flash['type'] ?? '') ?>" role="<?= ($flash['type'] ?? '') === 'error' ? 'alert' : 'status' ?>">
                        <?= e($flash['message'] ?? '') ?>
                    </p>
                    <?php endif; ?>
                    <form class="contact-form" method="post" action="contact.php">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <label for="contact-name">Nama</label>
                        <input id="contact-name" name="name" autocomplete="name" maxlength="150" required
                            value="<?= e($contactForm['name'] ?? '') ?>">
                        <label for="contact-email">Email</label>
                        <input id="contact-email" type="email" name="email" autocomplete="email" maxlength="190"
                            required value="<?= e($contactForm['email'] ?? '') ?>">
                        <label for="contact-subject">Subjek</label>
                        <input id="contact-subject" name="subject" maxlength="190" required
                            value="<?= e($contactForm['subject'] ?? '') ?>">
                        <label for="contact-message">Pesan</label>
                        <textarea id="contact-message" name="message" rows="5" maxlength="5000"
                            required><?= e($contactForm['message'] ?? '') ?></textarea>
                        <button class="button button-primary" type="submit">Kirim pesan</button>
                    </form>
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