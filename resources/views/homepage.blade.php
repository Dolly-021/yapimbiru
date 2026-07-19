<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SMK YAPIM BIRU-BIRU</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="icon" href="{{ (asset('images/yapim.png')) }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .gradient-text {
      background: linear-gradient(135deg, #fff 0%, #e0f2fe 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .animate-float {
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {

      0%,
      100% {
        transform: translateY(0px);
      }

      50% {
        transform: translateY(-20px);
      }
    }

    .animate-pulse-slow {
      animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .glass-effect {
      background: rgba(255, 255, 255, 0.08);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .feature-card {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .feature-card:hover {
      transform: translateY(-12px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
    }

    .stats-number {
      font-size: 3rem;
      font-weight: 800;
      line-height: 1;
    }
  </style>
</head>

<body class="bg-linear-to-br from-[#0369a1] via-[#06b6d4] to-[#14b8a6] min-h-screen text-white overflow-x-hidden">

  <!-- Animated Background Elements -->
  <div class="fixed inset-0 overflow-hidden pointer-events-none">
    <div
      class="absolute -top-40 -right-40 w-80 h-80 bg-[#06b6d4] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow">
    </div>
    <div
      class="absolute -bottom-40 -left-40 w-80 h-80 bg-[#14b8a6] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow"
      style="animation-delay: 2s;"></div>
    <div
      class="absolute top-1/2 left-1/2 w-80 h-80 bg-[#0369a1] rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse-slow"
      style="animation-delay: 4s;"></div>
  </div>

  <!-- Navbar -->
  <header class="relative z-10 w-full px-6 lg:px-16 py-6">
    <nav class="glass-effect rounded-2xl px-8 py-4 shadow-2xl">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
          <img src="{{ asset('images/yapim.png') }}" alt="Logo" class="w-14 h-auto object-contain rounded-xl shadow-lg">
          <div>
            <h1 class="font-bold text-xl tracking-tight">SMK YAPIM BIRU-BIRU</h1>
            <p class="text-xs text-cyan-200">Sistem Informasi Akademik</p>
          </div>
        </div>
        <div class="flex gap-3">
          <a href="{{ route('login') }}"
            class="glass-effect hover:bg-white/20 px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2 group">
            <i class="fas fa-sign-in-alt group-hover:translate-x-1 transition-transform"></i>
            Login
          </a>
          <a href="{{ route('register') }}"
            class="bg-linear-to-r from-[#0369a1] to-[#06b6d4] hover:from-[#025a8a] hover:to-[#0891b2] px-6 py-2.5 rounded-xl font-semibold shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
            <i class="fas fa-user-plus"></i>
            Register
          </a>
        </div>
      </div>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="relative z-10 text-center mt-16 px-6 max-w-6xl mx-auto">
    <div class="animate-float">
      <div class="inline-block mb-4">
        <span class="glass-effect px-6 py-2 rounded-full text-sm font-semibold">
          <i class="fas fa-sparkles text-yellow-300"></i> Platform Digital Terpadu
        </span>
      </div>
      <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight">
        Sistem Informasi Akademik
        <br>
        <span class="gradient-text">SMK YAPIM BIRU-BIRU</span>
      </h1>
      <p class="text-cyan-100 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed mb-8">
        Platform digital terpadu untuk mengelola kegiatan akademik, mempermudah proses pembelajaran,
        dan meningkatkan komunikasi antara guru, siswa, dan sekolah.
      </p>
      <div class="flex justify-center gap-4 flex-wrap">
        <a href="{{ route('login') }}"
          class="bg-white text-blue-900 hover:bg-gray-100 px-8 py-4 rounded-xl font-bold shadow-2xl hover:shadow-3xl transition-all duration-300 flex items-center gap-2">
          <i class="fas fa-rocket"></i>
          Mulai Sekarang
        </a>
        <a href="#fitur"
          class="glass-effect hover:bg-white/20 px-8 py-4 rounded-xl font-bold transition-all duration-300 flex items-center gap-2">
          <i class="fas fa-info-circle"></i>
          Pelajari Lebih Lanjut
        </a>
      </div>
    </div>
  </section>

  <!-- Stats Section -->
  <section class="relative z-10 mt-24 px-6 max-w-6xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="glass-effect rounded-2xl p-8 text-center">
        <div class="stats-number gradient-text">500+</div>
        <p class="text-cyan-100 mt-2 font-semibold">Siswa Aktif</p>
      </div>
      <div class="glass-effect rounded-2xl p-8 text-center">
        <div class="stats-number gradient-text">50+</div>
        <p class="text-cyan-100 mt-2 font-semibold">Tenaga Pengajar</p>
      </div>
      <div class="glass-effect rounded-2xl p-8 text-center">
        <div class="stats-number gradient-text">20+</div>
        <p class="text-cyan-100 mt-2 font-semibold">Program Keahlian</p>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section id="fitur" class="relative z-10 mt-24 px-6 max-w-7xl mx-auto pb-24">
    <div class="text-center mb-16">
      <h2 class="text-4xl md:text-5xl font-extrabold mb-4">Fitur Unggulan</h2>
      <p class="text-cyan-100 text-lg max-w-2xl mx-auto">
        Nikmati berbagai fitur canggih yang dirancang untuk memudahkan aktivitas akademik Anda
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <!-- Portal Siswa -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-graduation-cap text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Portal Siswa</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Akses mudah ke jadwal pelajaran, materi pembelajaran, tugas, dan informasi akademik lengkap dalam satu
          platform.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-blue-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Materi</span>
          <span class="bg-cyan-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Tugas</span>
          <span class="bg-teal-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Nilai</span>
        </div>
      </div>

      <!-- Portal Guru -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-chalkboard-teacher text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Portal Guru</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Kelola kelas, unggah materi, buat tugas, dan pantau perkembangan siswa dengan dashboard yang intuitif.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-purple-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Upload</span>
          <span class="bg-pink-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Monitor</span>
          <span class="bg-red-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Penilaian</span>
        </div>
      </div>

      <!-- Presensi Digital -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-green-400 to-emerald-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-clipboard-check text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Presensi Digital</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Sistem presensi digital real-time yang terintegrasi untuk pencatatan kehadiran yang akurat dan efisien.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-green-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Real-time</span>
          <span class="bg-emerald-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Otomatis</span>
          <span class="bg-teal-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Laporan</span>
        </div>
      </div>

      <!-- Manajemen Izin -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-file-medical text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Pengajuan Izin</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Ajukan izin atau surat sakit secara online dengan upload bukti digital. Proses approval yang cepat dan
          transparan.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-yellow-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Online</span>
          <span class="bg-orange-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Tracking</span>
        </div>
      </div>

      <!-- Laporan Akademik -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-indigo-400 to-blue-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-chart-line text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Laporan & Analitik</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Dashboard analitik lengkap dengan grafik dan statistik untuk monitoring performa akademik secara menyeluruh.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-indigo-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Dashboard</span>
          <span class="bg-blue-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Export</span>
        </div>
      </div>

      <!-- Komunikasi -->
      <div class="feature-card glass-effect rounded-3xl p-8 shadow-2xl">
        <div
          class="w-16 h-16 bg-linear-to-br from-rose-400 to-red-500 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
          <i class="fas fa-comments text-3xl text-white"></i>
        </div>
        <h3 class="text-2xl font-bold mb-4">Komunikasi Terpadu</h3>
        <p class="text-cyan-100 leading-relaxed mb-6">
          Komunikasi efektif antara guru, siswa, dan orang tua melalui notifikasi dan pengumuman real-time.
        </p>
        <div class="flex gap-2 flex-wrap">
          <span class="bg-rose-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Notifikasi</span>
          <span class="bg-red-500/30 px-3 py-1 rounded-lg text-xs font-semibold">Alert</span>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="relative z-10 px-6 pb-24">
    <div class="max-w-5xl mx-auto glass-effect rounded-3xl p-12 text-center shadow-2xl">
      <h2 class="text-4xl md:text-5xl font-extrabold mb-6">
        Siap Memulai Perjalanan Digital Anda?
      </h2>
      <p class="text-cyan-100 text-lg mb-8 max-w-2xl mx-auto">
        Bergabunglah dengan ribuan siswa dan guru yang telah merasakan kemudahan sistem kami
      </p>
      <div class="flex justify-center gap-4 flex-wrap">
        <a href="{{ route('register.siswa') }}"
          class="bg-linear-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 px-8 py-4 rounded-xl font-bold shadow-xl transition-all duration-300 flex items-center gap-2">
          <i class="fas fa-user-graduate"></i>
          Daftar Sebagai Siswa
        </a>
        <a href="{{ route('register.guru') }}"
          class="bg-linear-to-r from-purple-500 to-pink-500 hover:from-purple-600 hover:to-pink-600 px-8 py-4 rounded-xl font-bold shadow-xl transition-all duration-300 flex items-center gap-2">
          <i class="fas fa-user-tie"></i>
          Daftar Sebagai Guru
        </a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="relative z-10 glass-effect mt-12 py-8">
    <div class="max-w-6xl mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <div>
          <div class="flex items-center gap-3 mb-4">
            <img src="{{ asset('images/yapim.png') }}" alt="Logo" class="w-14 h-auto object-contain rounded-lg">
            <h3 class="font-bold text-lg">SMK YAPIM BIRU-BIRU</h3>
          </div>
          <p class="text-cyan-100 text-sm">
            Platform digital terpadu untuk mengelola kegiatan akademik dengan efisien dan modern.
          </p>
        </div>
        <div>
          <h4 class="font-bold mb-4">Link Cepat</h4>
          <ul class="space-y-2 text-cyan-100 text-sm">
            <li><a href="{{ route('login') }}" class="hover:text-white transition-colors"><i
                  class="fas fa-chevron-right text-xs"></i> Login</a></li>
            <li><a href="{{ route('register') }}" class="hover:text-white transition-colors"><i
                  class="fas fa-chevron-right text-xs"></i> Register</a></li>
            <li><a href="#fitur" class="hover:text-white transition-colors"><i class="fas fa-chevron-right text-xs"></i>
                Fitur</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-bold mb-4">Kontak</h4>
          <ul class="space-y-2 text-cyan-100 text-sm">
            <li><i class="fas fa-map-marker-alt text-cyan-300"></i> Biru-biru, Deli Serdang</li>
            <li><i class="fas fa-phone text-cyan-300"></i> (061) XXX-XXXX</li>
            <li><i class="fas fa-envelope text-cyan-300"></i> info@smkyapim.sch.id</li>
          </ul>
        </div>
      </div>
      <div class="border-t border-white/10 pt-6 text-center text-cyan-100 text-sm">
        <p>© 2025 SMK YAPIM BIRU-BIRU. Semua hak dilindungi.</p>
      </div>
    </div>
  </footer>

  <!-- Smooth Scroll -->
  <script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    });
  </script>

</body>

</html>