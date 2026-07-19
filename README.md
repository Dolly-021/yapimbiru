# WEBSITE SMK YAPIM BIRU BIRU

Kelompok 5 :
- **Dolly Efredi Bukit**                        - 241402021 - *Project Manager, Backend*
- **Arialdi Manday**                            - 241402006 - *Frontend & UI/UX*
- **Cindy Samosir**                             - 241402009 - *Frontend*
- **Vincent Josechristian Andreas Simbolon**    - 241402039 - *Backend*
- **Chaterine Eklesia Maryati**                 - 241402123 - *Frontend*

## Description

Website Sistem Informasi Manajemen Sekolah SMK Yapim Biru-Biru merupakan aplikasi berbasis web yang dirancang untuk membantu digitalisasi proses akademik dan administrasi sekolah.
Sistem ini mengintegrasikan berbagai fitur seperti presensi digital guru dan siswa, pengelolaan materi pembelajaran dan tugas, serta dashboard pemantauan aktivitas sekolah secara real-time.

Tujuan utamanya adalah meningkatkan efisiensi, akurasi, dan transparansi dalam pengelolaan data sekolah dengan menerapkan konsep Manajemen Sistem Basis Data berbasis web.
Proyek ini dikembangkan menggunakan Laravel (PHP Framework), MySQL, CSS, dan JavaScript.

User dan Perannya:

1. Admin
- Memiliki akses teknis penuh terhadap sistem. Admin bertanggung jawab mengelola database, membuat akun pengguna (kepala sekolah, guru, siswa, pembina), mengatur jadwal, serta melakukan backup data dan pemeliharaan sistem.

Kepala Sekolah
- Dapat memantau keseluruhan aktivitas akademik dan kehadiran guru maupun siswa. Kepala sekolah juga memiliki hak untuk menyetujui izin, mengevaluasi laporan, serta mengunduh rekap data untuk dokumentasi resmi.

Pembina Sekolah
- Berfungsi sebagai pihak pengawas yang dapat melihat data kehadiran, materi pembelajaran, dan laporan aktivitas guru serta siswa tanpa hak mengubah data. Pembina juga dapat memberikan catatan evaluasi.

Guru
- Melakukan presensi, mencatat kehadiran siswa di kelas, mengunggah materi dan tugas, serta memvalidasi izin siswa. Guru juga dapat melihat rekap presensi per kelas untuk keperluan evaluasi pembelajaran.

Siswa
- Melakukan presensi harian melalui akun masing-masing, mengunduh materi pembelajaran, mengumpulkan tugas, serta mengajukan izin tidak hadir disertai bukti pendukung.


## Tech Stack

- **Composer v2.4.1**
- **PHP v8.3.22**
- **Laragon v6.0**
- **MySQL v15.1**
- **Laravel v12.32.5**

### Set Up dan Run Project

Clone repository

    git clone https://github.com/vincentjcas/msbd.git
    cd msbd

Install dependencies laravel

    composer install

Buat file .env dengan menyalin .env.example

    cp .env.example .env

Install depencies Front-End

    npm install && npm run dev

Konfigurasi .env dan sesuaikan dengan database lokal

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=db_simak_smk
    DB_USERNAME=kel5
    DB_PASSWORD=tubessialan

Generate App Key

    php artisan key:generate

Jalankan migrasi database

    php artisan migrate

Jalankan server

    php artisan serve

Akses sistem

    http://127.0.0.1:8000