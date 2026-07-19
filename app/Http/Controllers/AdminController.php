<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\KepalaSekolah;
use App\Models\Pembina;
use App\Models\Kelas;
use App\Models\Jadwal;
use App\Models\BackupLog;
use App\Services\LogActivityService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    protected $logActivity;

    public function __construct(LogActivityService $logActivity)
    {
        $this->logActivity = $logActivity;
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalGuru = User::where('role', 'guru')->where('status_approval', 'approved')->count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalKelas = Kelas::count();
        $pendingSiswa = User::where('role', 'siswa')->where('status_aktif', 0)->count();
        $pendingGuru = User::where('role', 'guru')->where('status_approval', 'pending')->count();
        $lastBackup = BackupLog::latest()->first();
        
        return view('admin.dashboard', compact('totalUsers', 'totalGuru', 'totalSiswa', 'totalKelas', 'pendingSiswa', 'pendingGuru', 'lastBackup'));
    }

    public function users()
    {
        $search = request('search');
        $role = request('role');
        $status = request('status');

        $users = User::with(['guru', 'siswa', 'kepalaSekolah', 'pembina'])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('nama_lengkap', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($status !== null && $status !== '', function($query) use ($status) {
                if ($status === 'pending') {
                    $query->where('status_approval', 'pending');
                } elseif ($status === 'approved') {
                    $query->where('status_approval', 'approved');
                } elseif ($status === 'active') {
                    $query->where('status_aktif', 1);
                } elseif ($status === 'inactive') {
                    $query->where('status_aktif', 0);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    public function showUser($id)
    {
        $user = User::with(['guru', 'siswa', 'kepalaSekolah', 'pembina'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function toggleUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status_aktif = !$user->status_aktif;
            $user->save();

            $status = $user->status_aktif ? 'aktif' : 'nonaktif';
            $this->logActivity->log('update_status', auth()->user()->id_user, "Ubah status user {$user->username} menjadi {$status}");

            return back()->with('success', "Status user berhasil diubah menjadi {$status}");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    public function createUser()
    {
        $kelas = Kelas::all();
        return view('admin.users.create', compact('kelas'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,kepala_sekolah,pembina,guru,siswa',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'no_hp' => 'nullable|string|max:15',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'status_aktif' => 1,
            ]);

            if ($request->role === 'guru') {
                Guru::create([
                    'id_user' => $user->id_user,
                    'nip' => $request->nip,
                    'mata_pelajaran' => $request->mata_pelajaran,
                    'jabatan' => $request->jabatan,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                ]);
            } elseif ($request->role === 'siswa') {
                Siswa::create([
                    'id_user' => $user->id_user,
                    'id_kelas' => $request->id_kelas,
                    'nis' => $request->nis,
                    'nisn' => $request->nisn,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'nama_ortu' => $request->nama_ortu,
                    'no_hp_ortu' => $request->no_hp_ortu,
                ]);
            } elseif ($request->role === 'kepala_sekolah') {
                KepalaSekolah::create([
                    'id_user' => $user->id_user,
                    'nip' => $request->nip,
                    'periode_mulai' => $request->periode_mulai,
                    'periode_selesai' => $request->periode_selesai,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                ]);
            } elseif ($request->role === 'pembina') {
                Pembina::create([
                    'id_user' => $user->id_user,
                    'nip' => $request->nip,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                ]);
            }

            $this->logActivity->logCrud('create', auth()->user()->id_user, 'users', $user->id_user);

            DB::commit();
            return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function editUser($id)
    {
        $user = User::with(['guru', 'siswa', 'kepalaSekolah', 'pembina'])->findOrFail($id);
        $kelas = Kelas::all();
        return view('admin.users.edit', compact('user', 'kelas'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => 'required|unique:users,username,' . $id . ',id_user',
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'no_hp' => 'nullable|string|max:15',
            'status_aktif' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'username' => $request->username,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'status_aktif' => $request->status_aktif,
            ]);

            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($user->role === 'guru' && $user->guru) {
                $user->guru->update([
                    'nip' => $request->nip,
                    'mata_pelajaran' => $request->mata_pelajaran,
                    'jabatan' => $request->jabatan,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                ]);
            } elseif ($user->role === 'siswa' && $user->siswa) {
                $user->siswa->update([
                    'id_kelas' => $request->id_kelas,
                    'nis' => $request->nis,
                    'nisn' => $request->nisn,
                    'alamat' => $request->alamat,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'nama_ortu' => $request->nama_ortu,
                    'no_hp_ortu' => $request->no_hp_ortu,
                ]);
            }

            $this->logActivity->logCrud('update', auth()->user()->id_user, 'users', $user->id_user);

            DB::commit();
            return redirect()->route('admin.users')->with('success', 'User berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate user: ' . $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            
            if ($user->role === 'siswa' && $user->siswa) {
                db_procedure()->hapusSiswa($user->siswa->id_siswa);
            } else {
                $user->delete();
            }

            $this->logActivity->logCrud('delete', auth()->user()->id_user, 'users', $id);

            DB::commit();
            return redirect()->route('admin.users')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function jadwal()
    {
        $jadwal = Jadwal::with(['kelas', 'guru.user'])->orderBy('hari')->get();
        return view('admin.jadwal.jadwal-list', compact('jadwal'));
    }

    public function createJadwal()
    {
        $kelas = Kelas::all();
        $guru = Guru::with('user')->get();
        return view('admin.jadwal.create', compact('kelas', 'guru'));
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'required|exists:guru,id_guru',
            'mata_pelajaran' => 'required|string|max:100',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal = Jadwal::create($request->all());
        $this->logActivity->logCrud('create', auth()->user()->id_user, 'jadwal_pelajaran', $jadwal->id_jadwal);

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function updateJadwal(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_guru' => 'required|exists:guru,id_guru',
            'mata_pelajaran' => 'required|string|max:100',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        $jadwal->update($request->all());
        $this->logActivity->logCrud('update', auth()->user()->id_user, 'jadwal_pelajaran', $jadwal->id_jadwal);

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil diupdate');
    }

    public function deleteJadwal($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        
        $this->logActivity->logCrud('delete', auth()->user()->id_user, 'jadwal_pelajaran', $id);

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil dihapus');
    }

    public function backup()
    {
        $backups = BackupLog::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.backup', compact('backups'));
    }

    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $backupPath = storage_path('app/backups/' . $filename);
            
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Get database credentials
            $host = config('database.connections.mysql.host');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $database = config('database.connections.mysql.database');

            // Build mysqldump command with CUSTOM export options (same as phpMyAdmin custom export)
            $mysqldumpOptions = [
                '--host=' . escapeshellarg($host),
                '--user=' . escapeshellarg($username),
                '--password=' . escapeshellarg($password),
                '--single-transaction',           // Enclose export in a transaction
                '--routines',                      // Include stored procedures and functions
                '--triggers',                      // Include triggers
                '--events',                        // Include events
                '--add-drop-database',            // Add DROP DATABASE statement
                '--add-drop-table',               // Add DROP TABLE/VIEW statement
                '--databases ' . escapeshellarg($database), // Include CREATE DATABASE / USE statement
                '--hex-blob',                     // Dump binary columns in hexadecimal notation
                '--tz-utc',                       // Dump TIMESTAMP in UTC
                '--complete-insert',              // Include column names in INSERT statements
                '--extended-insert',              // Use multiple-row INSERT syntax
                '--quote-names',                  // Enclose table/column names with backticks
                '--add-locks',                    // Add LOCK TABLES statements
                '--create-options',               // Include all table creation options
                '--disable-keys',                 // Speed up import with /*!40000 ALTER TABLE ... DISABLE KEYS */
                '--result-file=' . escapeshellarg($backupPath),
            ];

            $command = 'mysqldump ' . implode(' ', $mysqldumpOptions) . ' 2>&1';

            exec($command, $output, $returnVar);

            if ($returnVar === 0 && file_exists($backupPath) && filesize($backupPath) > 0) {
                
                // Append Views to backup file (mysqldump doesn't handle views well)
                $this->appendViewsToBackup($backupPath, $database);
                
                $fileSize = filesize($backupPath);
                
                $backup = BackupLog::create([
                    'nama_file' => $filename,
                    'ukuran_file' => $fileSize,
                    'lokasi_file' => $backupPath,
                    'dibuat_oleh' => auth()->user()->id_user,
                    'keterangan' => 'Backup database custom (Procedures, Functions, Triggers, Views)',
                ]);

                $this->logActivity->log('backup', auth()->user()->id_user, 'Backup database berhasil dibuat: ' . $filename);

                return redirect()->route('admin.backup')->with('success', 'Backup berhasil dibuat! Klik download untuk mengunduh file.');
            } else {
                // Delete failed backup file if exists
                if (file_exists($backupPath)) {
                    unlink($backupPath);
                }
                
                $errorMsg = implode("\n", $output);
                return back()->with('error', 'Gagal membuat backup. ' . $errorMsg);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    private function appendViewsToBackup($backupPath, $database)
    {
        try {
            // Get all views from database
            $views = DB::select("SELECT TABLE_NAME FROM information_schema.VIEWS WHERE TABLE_SCHEMA = ?", [$database]);
            
            if (empty($views)) {
                return; // No views to export
            }

            $viewsContent = "\n\n-- --------------------------------------------------------\n";
            $viewsContent .= "-- Views exported as TABLES (for compatibility)\n";
            $viewsContent .= "-- --------------------------------------------------------\n\n";

            foreach ($views as $view) {
                $viewName = $view->TABLE_NAME;
                
                // Get view structure
                $createView = DB::select("SHOW CREATE TABLE `{$viewName}`");
                
                if (!empty($createView)) {
                    $viewsContent .= "-- --------------------------------------------------------\n\n";
                    $viewsContent .= "-- Table structure for table `{$viewName}`\n";
                    $viewsContent .= "--\n\n";
                    $viewsContent .= "DROP TABLE IF EXISTS `{$viewName}`;\n";
                    
                    // Get columns
                    $columns = DB::select("DESCRIBE `{$viewName}`");
                    $viewsContent .= "CREATE TABLE IF NOT EXISTS `{$viewName}` (\n";
                    
                    $columnDefs = [];
                    foreach ($columns as $column) {
                        $columnDef = "  `{$column->Field}` {$column->Type}";
                        if ($column->Null === 'NO') {
                            $columnDef .= " NOT NULL";
                        }
                        if ($column->Default !== null) {
                            $columnDef .= " DEFAULT '{$column->Default}'";
                        }
                        $columnDefs[] = $columnDef;
                    }
                    
                    $viewsContent .= implode(",\n", $columnDefs);
                    $viewsContent .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";
                }
            }

            // Append to backup file
            file_put_contents($backupPath, $viewsContent, FILE_APPEND);
            
        } catch (\Exception $e) {
            // If views export fails, continue without them
            // Log the error but don't fail the whole backup
            \Log::warning('Failed to export views: ' . $e->getMessage());
        }
    }

    public function downloadBackup($id)
    {
        try {
            $backup = BackupLog::findOrFail($id);
            
            if (!file_exists($backup->lokasi_file)) {
                return back()->with('error', 'File backup tidak ditemukan');
            }

            $this->logActivity->log('download_backup', auth()->user()->id_user, 'Download backup: ' . $backup->nama_file);

            return response()->download($backup->lokasi_file, $backup->nama_file);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal download backup: ' . $e->getMessage());
        }
    }

    public function deleteBackup($id)
    {
        try {
            $backup = BackupLog::findOrFail($id);
            
            // Delete physical file
            if (file_exists($backup->lokasi_file)) {
                unlink($backup->lokasi_file);
            }

            $this->logActivity->log('delete_backup', auth()->user()->id_user, 'Hapus backup: ' . $backup->nama_file);

            // Delete record
            $backup->delete();

            return redirect()->route('admin.backup')->with('success', 'Backup berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal hapus backup: ' . $e->getMessage());
        }
    }

    public function logAktivitas()
    {
        $logs = \App\Models\LogAktivitas::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('admin.log-aktivitas', compact('logs'));
    }

    /*
    ========================================
    SISTEM VERIFIKASI GURU - DISABLED
    ========================================
    Sistem verifikasi guru tidak lagi digunakan.
    Guru sekarang langsung aktif setelah registrasi.
    Methods ini di-comment untuk backward compatibility.
    ========================================
    */

    // Tampilkan halaman verifikasi guru yang pending
    public function verifikasiGuru()
    {
        $pendingGuru = User::where('role', 'guru')
            ->where('status_approval', 'pending')
            ->with('guru')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.verifikasi-guru', compact('pendingGuru'));
    }

    // Approve pendaftaran guru
    public function approveGuru($id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id_user', $id)
                ->where('role', 'guru')
                ->where('status_approval', 'pending')
                ->firstOrFail();
            
            $user->update(['status_approval' => 'approved']);
            
            $this->logActivity->log('approve_guru', auth()->user()->id_user, "Approve pendaftaran guru: {$user->nama_lengkap} (ID: {$user->id_user})");
            
            DB::commit();
            return redirect()->route('admin.verifikasi-guru')->with('success', "Pendaftaran guru {$user->nama_lengkap} berhasil disetujui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui pendaftaran: ' . $e->getMessage());
        }
    }

    // Reject pendaftaran guru
    public function rejectGuru(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id_user', $id)
                ->where('role', 'guru')
                ->where('status_approval', 'pending')
                ->firstOrFail();
            
            $namaGuru = $user->nama_lengkap;
            $alasan = $request->input('alasan', 'Tidak ada alasan');
            
            // Update status menjadi rejected (tidak dihapus)
            $user->update(['status_approval' => 'rejected']);
            
            $this->logActivity->log('reject_guru', auth()->user()->id_user, "Reject pendaftaran guru: {$namaGuru} (ID: {$id}). Alasan: {$alasan}");
            
            DB::commit();
            return redirect()->route('admin.verifikasi-guru')->with('success', "Pendaftaran guru {$namaGuru} berhasil ditolak.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pendaftaran: ' . $e->getMessage());
        }
    }

    /*
    ========================================
    SISTEM VERIFIKASI SISWA BARU
    ========================================
    Sistem ini untuk verifikasi siswa yang mendaftar
    dengan NIS yang TIDAK ADA di data master.
    Siswa dengan NIS terdaftar langsung aktif.
    ========================================
    */

    /**
     * Tampilkan halaman verifikasi siswa yang pending
     */
    public function verifikasiSiswa()
    {
        $pendingSiswa = User::with(['siswa.kelas'])
            ->where('role', 'siswa')
            ->where('status_aktif', 0)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.verifikasi-siswa', compact('pendingSiswa'));
    }

    /**
     * Approve pendaftaran siswa
     */
    public function approveSiswa($id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id_user', $id)
                ->where('role', 'siswa')
                ->where('status_aktif', 0)
                ->firstOrFail();
            
            $user->update(['status_aktif' => 1]);
            
            $this->logActivity->log('approve_siswa', auth()->user()->id_user, "Approve pendaftaran siswa: {$user->nama_lengkap} (ID: {$user->id_user})");
            
            DB::commit();
            return redirect()->route('admin.verifikasi-siswa')->with('success', "Pendaftaran siswa {$user->nama_lengkap} berhasil disetujui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Reject pendaftaran siswa dan hapus akun
     */
    public function rejectSiswa(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::where('id_user', $id)
                ->where('role', 'siswa')
                ->where('status_aktif', 0)
                ->firstOrFail();
            
            $namaSiswa = $user->nama_lengkap;
            $alasan = $request->input('alasan', 'Tidak ada alasan');
            
            // Hapus data siswa terlebih dahulu (karena foreign key constraint)
            if ($user->siswa) {
                $user->siswa->delete();
            }
            
            // Hapus user
            $user->delete();
            
            $this->logActivity->log('reject_siswa', auth()->user()->id_user, "Reject pendaftaran siswa: {$namaSiswa} (ID: {$id}). Alasan: {$alasan}");
            
            DB::commit();
            return redirect()->route('admin.verifikasi-siswa')->with('success', "Pendaftaran siswa {$namaSiswa} berhasil ditolak dan dihapus.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * File Materi - Monitoring materi yang diunggah guru
     */
    public function fileMateri()
    {
        $materi = \App\Models\Materi::with(['guru.user', 'kelas'])
            ->orderBy('uploaded_at', 'desc')
            ->paginate(20);
        
        return view('admin.file-materi', compact('materi'));
    }

    /**
     * Hapus file materi
     */
    public function deleteMateri($id)
    {
        DB::beginTransaction();
        try {
            $materi = \App\Models\Materi::findOrFail($id);
            
            // Hapus file jika ada
            if ($materi->file_materi && \Storage::exists('public/materi/' . $materi->file_materi)) {
                \Storage::delete('public/materi/' . $materi->file_materi);
            }
            
            $judulMateri = $materi->judul_materi;
            $materi->delete();
            
            $this->logActivity->log('delete_materi', auth()->user()->id_user, "Hapus materi: {$judulMateri} (ID: {$id})");
            
            DB::commit();
            return redirect()->route('admin.file-materi')->with('success', 'Materi berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete materi
     */
    public function bulkDeleteMateri(Request $request)
    {
        $request->validate([
            'materi_ids' => 'required|array|min:1',
            'materi_ids.*' => 'exists:materi,id_materi'
        ]);

        DB::beginTransaction();
        try {
            $materiIds = $request->materi_ids;
            $deletedCount = 0;
            $deletedTitles = [];
            
            foreach ($materiIds as $id) {
                $materi = \App\Models\Materi::find($id);
                if ($materi) {
                    // Hapus file jika ada
                    if ($materi->file_materi && \Storage::exists('public/materi/' . $materi->file_materi)) {
                        \Storage::delete('public/materi/' . $materi->file_materi);
                    }
                    
                    $deletedTitles[] = $materi->judul_materi;
                    $materi->delete();
                    $deletedCount++;
                }
            }
            
            $titles = implode(', ', array_slice($deletedTitles, 0, 3)) . ($deletedCount > 3 ? '...' : '');
            $this->logActivity->log('bulk_delete_materi', auth()->user()->id_user, "Bulk hapus {$deletedCount} materi: {$titles}");
            
            DB::commit();
            return redirect()->route('admin.file-materi')->with('success', "Berhasil menghapus {$deletedCount} materi");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus materi: ' . $e->getMessage());
        }
    }

    /**
     * Kegiatan Sekolah - Daftar kegiatan
     */
    public function kegiatan()
    {
        $kegiatan = \App\Models\Kegiatan::orderBy('tanggal', 'desc')->paginate(20);
        return view('admin.kegiatan.kegiatan-list', compact('kegiatan'));
    }

    /**
     * Form tambah kegiatan
     */
    public function createKegiatan()
    {
        return view('admin.kegiatan.create');
    }

    /**
     * Simpan kegiatan baru
     */
    public function storeKegiatan(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:rapat,ujian,acara_resmi,lainnya',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $kegiatan = \App\Models\Kegiatan::create($request->all());
            
            $this->logActivity->log('create_kegiatan', auth()->user()->id_user, "Tambah kegiatan: {$kegiatan->nama_kegiatan}");
            
            DB::commit();
            return redirect()->route('admin.kegiatan')->with('success', 'Kegiatan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Form edit kegiatan
     */
    public function editKegiatan($id)
    {
        $kegiatan = \App\Models\Kegiatan::findOrFail($id);
        return view('admin.kegiatan.edit', compact('kegiatan'));
    }

    /**
     * Update kegiatan
     */
    public function updateKegiatan(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'jenis_kegiatan' => 'required|in:rapat,ujian,acara_resmi,lainnya',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $kegiatan = \App\Models\Kegiatan::findOrFail($id);
            $kegiatan->update($request->all());
            
            $this->logActivity->log('update_kegiatan', auth()->user()->id_user, "Update kegiatan: {$kegiatan->nama_kegiatan}");
            
            DB::commit();
            return redirect()->route('admin.kegiatan')->with('success', 'Kegiatan berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengupdate kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Hapus kegiatan
     */
    public function deleteKegiatan($id)
    {
        DB::beginTransaction();
        try {
            $kegiatan = \App\Models\Kegiatan::findOrFail($id);
            $namaKegiatan = $kegiatan->nama_kegiatan;
            $kegiatan->delete();
            
            $this->logActivity->log('delete_kegiatan', auth()->user()->id_user, "Hapus kegiatan: {$namaKegiatan}");
            
            DB::commit();
            return redirect()->route('admin.kegiatan')->with('success', 'Kegiatan berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus kegiatan: ' . $e->getMessage());
        }
    }

    /**
     * Pengajuan Izin - Monitoring izin siswa
     */
    public function pengajuanIzin()
    {
        $izin = \App\Models\Izin::with(['siswa.user', 'siswa.kelas', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.pengajuan-izin', compact('izin'));
    }

    /**
     * Approve pengajuan izin
     */
    public function approveIzin($id)
    {
        DB::beginTransaction();
        try {
            $izin = \App\Models\Izin::findOrFail($id);
            $izin->update(['status' => 'disetujui']);
            
            $this->logActivity->log('approve_izin', auth()->user()->id_user, "Approve izin {$izin->tipe}: {$izin->siswa->user->nama_lengkap}");
            
            DB::commit();
            return redirect()->route('admin.pengajuan-izin')->with('success', 'Izin berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui izin: ' . $e->getMessage());
        }
    }

    /**
     * Reject pengajuan izin
     */
    public function rejectIzin(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $izin = \App\Models\Izin::findOrFail($id);
            $izin->update([
                'status' => 'ditolak',
                'keterangan' => $request->input('alasan', 'Tidak memenuhi syarat')
            ]);
            
            $this->logActivity->log('reject_izin', auth()->user()->id_user, "Reject izin {$izin->tipe}: {$izin->siswa->user->nama_lengkap}");
            
            DB::commit();
            return redirect()->route('admin.pengajuan-izin')->with('success', 'Izin berhasil ditolak');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak izin: ' . $e->getMessage());
        }
    }
}

