-- Struktur database utama portfolio.
CREATE DATABASE IF NOT EXISTS dhandy_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE dhandy_portfolio;

CREATE TABLE profile (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NULL,
    bio TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE education (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    institusi VARCHAR(190) NOT NULL,
    logo_file VARCHAR(190) NOT NULL,
    jurusan VARCHAR(190) NOT NULL,
    periode VARCHAR(50) NOT NULL,
    start_year SMALLINT UNSIGNED NOT NULL,
    deskripsi TEXT NOT NULL
);

CREATE TABLE certificates (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(190) NOT NULL,
    penerbit VARCHAR(190) NOT NULL,
    tanggal DATE NOT NULL,
    periode VARCHAR(80) NOT NULL,
    image_file VARCHAR(190) NOT NULL,
    deskripsi TEXT NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE skill_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    number VARCHAR(10) NOT NULL,
    judul VARCHAR(190) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE skills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    logo_file VARCHAR(190) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    CONSTRAINT fk_skills_group FOREIGN KEY (group_id) REFERENCES skill_groups(id) ON DELETE CASCADE
);

CREATE TABLE interests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(190) NOT NULL,
    deskripsi TEXT NOT NULL,
    ikon VARCHAR(40) NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_messages_created_at (created_at)
);

-- Data awal yang tampil saat database pertama kali diinisialisasi.
INSERT INTO profile (name, email, bio)
VALUES (
    'Dhandy Putra Fahruddin',
    NULL,
    'Saya senang belajar dengan mencoba langsung, merapikan ide yang kompleks, dan membagikan hasilnya dengan cara yang mudah dipahami. Ketertarikan pada Minecraft dan membaca membentuk cara saya memandang proses kreatif: sabar, sistematis, dan selalu terbuka pada kemungkinan baru.'
);

INSERT INTO education (
    institusi, logo_file, jurusan, periode, start_year, deskripsi
)
VALUES
    (
        'SMK Negeri 1 Cikedung',
        'edu-logos/smk-negeri-1-cikedung.png',
        'Rekayasa Perangkat Lunak',
        '2022 : 2025',
        2022,
        'Saya selama di SMK Negeri 1 Cikedung fokus pada belajar tentang dasar-dasar pemrograman, bahasa pemrograman, dan konsep dasar sistem komputer.'
    ),
    (
        'Universitas Jenderal Soedirman',
        'edu-logos/universitas-jenderal-soedirman.png',
        'Informatika',
        '2025 : 2029',
        2025,
        'Saya selama di Universitas Jenderal Soedirman fokus pada pengembangan perangkat lunak, algoritma, dan struktur data. Saya juga mengikuti internship dari UKM Soedirman Robotic Team sebagai staff System divisi KRTI - RP.'
    );

INSERT INTO certificates (
    judul, penerbit, periode, image_file, deskripsi, sort_order
)
VALUES (
    'Nama kegiatan atau sertifikat',
    'Nama penyelenggara atau organisasi',
    'Bulan / Tahun pelaksanaan',
    'certificates/sertifikat-placeholder.svg',
    'Isi deskripsi ringkas kegiatan atau kompetensi yang diperoleh.',
    1
);

INSERT INTO skill_groups (number, judul, sort_order) VALUES
('01', 'Bahasa pemrograman', 1), ('02', 'Framework dan perangkat', 2), ('03', 'AI dan alat produktivitas', 3);

INSERT INTO skills (group_id, name, logo_file, sort_order)
VALUES
    (1, 'HTML5', 'html5.svg', 1),
    (1, 'CSS3', 'css.svg', 2),
    (1, 'JavaScript', 'javascript.svg', 3),
    (1, 'TypeScript', 'typescript.svg', 4),
    (1, 'Go', 'go.svg', 5),
    (1, 'Java', 'openjdk.svg', 6),
    (2, 'Next.js', 'nextjs.svg', 1),
    (2, 'React', 'react.svg', 2),
    (2, 'Tailwind CSS', 'tailwindcss.svg', 3),
    (2, 'Node.js', 'nodejs.svg', 4),
    (2, 'Git', 'git.svg', 5),
    (3, 'Claude', 'claude.svg', 1);

INSERT INTO interests (judul, deskripsi, ikon, sort_order)
VALUES
    (
        'Pengembangan Web Fullstack',
        'Membangun pengalaman web yang terstruktur dan berguna, dari antarmuka yang mudah dipahami hingga logika di baliknya.',
        'code-block',
        1
    ),
    (
        'Alur Kerja AI dan Otomatisasi',
        'Mengeksplorasi cara AI dan otomatisasi dapat membantu pekerjaan sehari-hari menjadi lebih terstruktur.',
        'robot-brain',
        2
    ),
    (
        'Desain dan Logika Gim',
        'Mempelajari bagaimana aturan, sistem, dan ruang eksplorasi membentuk pengalaman bermain yang bermakna.',
        'pickaxe',
        3
    );
