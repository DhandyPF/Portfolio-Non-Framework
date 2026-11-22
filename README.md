# Portfolio Dhandy Putra Fahruddin

Website portfolio pribadi milik **Dhandy Putra Fahruddin**, mahasiswa Informatika Universitas Jenderal Soedirman yang tertarik pada pengembangan web fullstack, alur kerja AI dan otomasi, serta desain dan logika gim.

Portfolio ini menjadi ruang untuk memperkenalkan diri, perjalanan pendidikan, pengalaman organisasi, teknologi yang dipelajari, bidang yang sedang digeluti, dan cara Dhandy membangun sesuatu secara terstruktur serta terus belajar melalui proyek.

## Tentang Saya

Saya senang belajar dengan mencoba langsung, merapikan ide yang kompleks, dan membagikan hasilnya dengan cara yang mudah dipahami. Ketertarikan pada Minecraft membentuk cara saya merancang, bereksperimen, dan membangun secara bertahap. Kebiasaan membaca membantu saya memahami perspektif baru dan menjaga rasa ingin tahu tetap aktif dalam setiap proses.

Perjalanan akademis saya dimulai dari **SMK Negeri 1 Cikedung** pada jurusan Rekayasa Perangkat Lunak, kemudian melanjutkan studi **Informatika di Universitas Jenderal Soedirman**. Saya juga memiliki ketertarikan pada pengembangan sistem dan pengalaman sebagai staff System divisi KRTI - RP di UKM Soedirman Robotic Team.

## Teknologi yang Digunakan

- HTML5, CSS3, dan JavaScript untuk antarmuka serta interaksi
- PHP untuk pemrosesan halaman dan dashboard admin
- MySQL dan PDO untuk penyimpanan serta pengambilan data
- HTML5, CSS3, JavaScript, TypeScript, Go, dan Java
- Next.js, React, Tailwind CSS, Node.js, dan Git
- Claude sebagai alat produktivitas dan pengembangan alur kerja AI

## Fitur Website

- Profil dan bio pribadi
- Riwayat pendidikan
- Pengalaman kegiatan dan sertifikat
- Daftar bahasa, framework, alat, dan teknologi
- Bidang peminatan
- Informasi kontak dan tautan GitHub
- Tema terang dan gelap
- Navbar responsif dengan animasi saat masuk dan saat halaman digulir
- Dashboard admin untuk mengelola konten portfolio

## Menjalankan secara Lokal

Proyek ini dapat dijalankan melalui XAMPP atau server PHP bawaan.

1. Aktifkan Apache dan MySQL melalui XAMPP.
2. Pastikan folder proyek berada di `htdocs`, misalnya `D:\xampp\htdocs\portfolio-vanilla-php`.
3. Jalankan `sql/schema.sql` melalui phpMyAdmin atau MySQL untuk membuat database dan data awal.
4. Sesuaikan kredensial database pada `config.php` jika diperlukan.
5. Buka `http://localhost/portfolio-vanilla-php/`.
6. Buka `http://localhost/portfolio-vanilla-php/admin.php` untuk mengelola konten.

Kredensial admin awal:

- Nama pengguna: `admin`
- Kata sandi: `admin123`

Segera ganti kredensial dan hash kata sandi pada `config.php` sebelum website dipublikasikan.

## Struktur Utama

```text
.
|-- index.php       # Halaman portfolio
|-- admin.php       # Dashboard pengelolaan konten
|-- config.php      # Konfigurasi database dan admin
|-- db.php          # Koneksi PDO
|-- functions.php   # Pengambilan data dan utilitas
|-- sql/
|   `-- schema.sql  # Struktur dan data awal database
`-- assets/
    |-- css/        # Tampilan dan animasi
    |-- js/         # Interaksi halaman
    `-- images/     # Foto profil dan aset visual
```

## Kontak

- Email: `dhandy.pf@gmail.com`
- GitHub: [DhandyPF](https://github.com/DhandyPF)
