<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\DataSiswaMaster;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login user dengan auto-detect role
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Email/NIS wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $identifier = $request->identifier;
        $password = $request->password;

        $user = null;

        // Auto-detect: Coba cari di tabel siswa dulu (berdasarkan NIS)
        $siswa = Siswa::where('nis', $identifier)->first();
        if ($siswa) {
            $user = User::where('id_user', $siswa->id_user)
                       ->where('role', 'siswa')
                       ->first();
        }

        // Kalau tidak ketemu, coba cari di tabel users langsung (berdasarkan email atau username)
        // Ini untuk cover guru, admin, kepsek, pembina yang login pakai email
        if (!$user) {
            $user = User::where(function($query) use ($identifier) {
                $query->where('email', $identifier)
                      ->orWhere('username', $identifier);
            })->first();
        }

        // Cek apakah user ditemukan dan password cocok
        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withErrors([
                'identifier' => 'Email/NIS atau password salah.',
            ])->withInput();
        }

        // ✅ Cek apakah user sudah diaktifkan
        if (!$user->status_aktif) {
            return back()->withErrors([
                'identifier' => 'Akun Anda belum diaktifkan. Silakan tunggu persetujuan dari admin.',
            ])->withInput();
        }

        // Login user
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        // ✅ Simpan pesan selamat datang dengan nama depan
        $firstName = explode(' ', $user->nama_lengkap)[0];
        session()->flash('success', "Selamat datang, {$firstName}!");

        // ✅ Redirect otomatis sesuai role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'guru':
                return redirect()->route('guru.dashboard');
            case 'siswa':
                return redirect()->route('siswa.dashboard');
            case 'kepala_sekolah':
                return redirect()->route('kepala_sekolah.dashboard');
            case 'pembina':
                return redirect()->route('pembina.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }


    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Tampilkan form register guru
     */
    public function showRegisterGuruForm()
    {
        return view('auth.register-guru');
    }

    /**
     * Tampilkan form register siswa
     */
    public function showRegisterSiswaForm()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('auth.register-siswa', compact('kelas'));
    }

    /**
     * Proses register guru baru (tanpa approval admin)
     */
    public function registerGuru(Request $request)
    {
        // Validasi dengan custom rule untuk no_hp yang cek relasi dengan user
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|regex:/^[0-9]{10,15}$/|unique:users,no_hp|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'password' => 'required|confirmed|min:6',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP harus berisi 10-15 digit angka.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // Generate username dari email (bagian sebelum @)
            $baseUsername = strtolower(explode('@', $request->email)[0]);
            $username = $baseUsername;
            $counter = 1;

            // Jika username sudah ada, tambahkan angka
            while (User::where('username', $username)->exists()) {
                $username = $baseUsername . $counter;
                $counter++;
            }

            // Buat user - GURU MENUNGGU APPROVAL (status_approval = pending)
            $user = User::create([
                'username' => $username,
                'nama_lengkap' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'guru',
                'no_hp' => $request->no_hp,
                'status_aktif' => 1,
                'status_approval' => 'pending', // ✅ Menunggu approval admin
            ]);

            // Buat data guru
            Guru::create([
                'id_user' => $user->id_user,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
            ]);

            DB::commit();

            return redirect()->route('login')->with('success_sweet', [
                'title' => 'Registrasi Berhasil!',
                'message' => 'Akun Anda telah dibuat dan menunggu persetujuan dari admin. Anda akan dapat login setelah admin menyetujui akun Anda.',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Proses register siswa baru
     */
    public function registerSiswa(Request $request)
    {
        // Cek apakah NIS ada di data master untuk conditional validation
        $siswaMasterExists = DataSiswaMaster::where('nis', $request->nis)->exists();

        $rules = [
            'nis' => 'required|string|unique:siswa,nis|max:20',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'required|string|regex:/^[0-9]{10,15}$/|unique:siswa,no_hp|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'semester' => 'required|string|in:X Semester Ganjil 2025/2026,XI Semester Ganjil 2025/2026,XII Semester Ganjil 2025/2026',
            'sekolah_asal' => 'required|string|max:200',
            'alamat' => 'required|string|max:500',
            'password' => 'required|confirmed|min:6',
        ];

        // Jika NIS TIDAK ada di data master, maka name WAJIB diisi
        if (!$siswaMasterExists) {
            $rules['name'] = 'required|string|max:255';
        }

        $request->validate($rules, [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar.',
            'name.required' => 'Nama wajib diisi karena NIS tidak terdaftar di data master.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'no_hp.regex' => 'Nomor HP harus berisi 10-15 digit angka.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'agama.required' => 'Agama wajib dipilih.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas tidak valid.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid. Hanya semester Ganjil 2025/2026 yang tersedia.',
            'sekolah_asal.required' => 'Sekolah asal wajib diisi.',
            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.max' => 'Alamat maksimal 500 karakter.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // Cek apakah NIS ada di data master
            $siswaMaster = DataSiswaMaster::where('nis', $request->nis)->first();

            // Tentukan status aktif berdasarkan apakah NIS terdaftar di data master
            // NIS terdaftar = langsung aktif, NIS tidak terdaftar = pending approval
            $statusAktif = $siswaMaster ? 1 : 0;

            // Generate nama_lengkap dari data master atau dari input form
            $namaLengkap = $siswaMaster ? $siswaMaster->nama_siswa : $request->name;

            // Generate username dari NIS
            $username = 'siswa_' . $request->nis;

            // Buat user
            $user = User::create([
                'username' => $username,
                'nama_lengkap' => $namaLengkap,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa',
                'status_aktif' => $statusAktif,
            ]);

            // Buat data siswa
            Siswa::create([
                'id_user' => $user->id_user,
                'nis' => $request->nis,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'id_kelas' => $request->id_kelas,
                'semester' => $request->semester,
                'sekolah_asal' => $request->sekolah_asal,
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
            ]);

            // Update flag is_registered di data master jika ada
            if ($siswaMaster) {
                $siswaMaster->update(['is_registered' => true]);
            }

            DB::commit();

            // Pesan sukses berbeda tergantung status
            if ($statusAktif) {
                return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan akun Anda.');
            } else {
                return redirect()->route('login')->with('success', 'Registrasi berhasil! Akun Anda menunggu persetujuan admin. Anda akan dihubungi melalui email jika akun telah disetujui.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan form register (LEGACY - backward compatibility)
     */
    public function showRegisterForm()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('auth.register', compact('kelas'));
    }

    /**
     * Proses register user baru (LEGACY - backward compatibility)
     */
    public function register(Request $request)
{
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed|min:6',
        'role' => 'required|in:guru,siswa',
    ];

    // Jika role siswa, id_kelas wajib diisi
    if ($request->role === 'siswa') {
        $rules['id_kelas'] = 'required|exists:kelas,id_kelas';
    }

    $request->validate($rules, [
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.max' => 'Nama lengkap maksimal 255 karakter.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.confirmed' => 'Password tidak sama.',
        'password.min' => 'Panjang password harus 6 karakter atau lebih.',
        'role.required' => 'Role wajib dipilih.',
        'role.in' => 'Role tidak valid.',
        'id_kelas.required' => 'Kelas wajib dipilih untuk siswa.',
        'id_kelas.exists' => 'Kelas yang dipilih tidak valid.',
    ]);

    // Buat user
    // Guru perlu approval, jadi status_aktif = 0 (pending)
    // Siswa langsung aktif, status_aktif = 1
    $user = User::create([
        'username' => $request->name,
        'nama_lengkap' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'status_aktif' => $request->role === 'guru' ? 0 : 1,
    ]);

    // Buat data siswa/guru berdasarkan role
    if ($request->role === 'siswa') {
        Siswa::create([
            'id_user' => $user->id_user,
            'nis' => 'NIS' . str_pad($user->id_user, 6, '0', STR_PAD_LEFT), // Generate NIS otomatis
            'id_kelas' => $request->id_kelas, // Simpan kelas yang dipilih
        ]);
    } elseif ($request->role === 'guru') {
        Guru::create([
            'id_user' => $user->id_user,
            'nip' => 'NIP' . str_pad($user->id_user, 6, '0', STR_PAD_LEFT), // Generate NIP otomatis
        ]);
    }

    // ✅ Simpan pesan sukses ke session
    if ($request->role === 'guru') {
        session()->flash('success', "Pendaftaran berhasil! Akun Anda menunggu persetujuan admin.");
    } else {
        session()->flash('success', "Akun anda berhasil dibuat, {$user->nama_lengkap}!");
    }

    // ✅ Arahkan ke halaman login
    return redirect()->route('login');


    }

    /**
     * API endpoint untuk cek NIS siswa
     * Digunakan untuk autocomplete nama siswa saat registrasi
     */
    public function checkNis($nis)
    {
        // Prioritas 1: Cek apakah NIS sudah pernah terdaftar di tabel siswa
        $existingSiswa = Siswa::where('nis', $nis)->first();
        if ($existingSiswa) {
            return response()->json([
                'found' => true,
                'already_registered' => true,
                'message' => 'NIS ini sudah pernah terdaftar. Silakan login dengan akun Anda.'
            ]);
        }

        // Prioritas 2: Cek di data master siswa
        $siswa = DataSiswaMaster::where('nis', $nis)->first();

        if (!$siswa) {
            return response()->json([
                'found' => false,
                'message' => 'NIS tidak ditemukan dalam database.'
            ]);
        }

        return response()->json([
            'found' => true,
            'already_registered' => false,
            'data' => [
                'nama_siswa' => $siswa->nama_siswa,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_lahir' => $siswa->tempat_lahir,
                'tanggal_lahir' => $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d') : null,
                'agama' => $siswa->agama,
                'sekolah_asal' => $siswa->sekolah_asal,
                'alamat' => $siswa->alamat,
                'no_hp' => $siswa->no_hp,
                'id_kelas' => $siswa->id_kelas,
                'nama_kelas' => $siswa->nama_kelas,
            ]
        ]);
    }
}
