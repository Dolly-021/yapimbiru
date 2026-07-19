<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use App\Models\Izin;
use App\Models\Kegiatan;
use App\Models\PresensiSiswa;
use App\Models\Views\VStatusIzinSiswa;
use App\Services\DatabaseFunctionService;
use App\Services\LogActivityService;
use Illuminate\Support\Facades\Storage;

class SiswaController extends Controller
{
    protected $dbFunction;
    protected $logActivity;

    public function __construct(DatabaseFunctionService $dbFunction, LogActivityService $logActivity)
    {
        $this->dbFunction = $dbFunction;
        $this->logActivity = $logActivity;
    }

    public function dashboard()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('login')->with('error', 'Data siswa tidak ditemukan atau belum dikaitkan dengan akun ini.');

        }

        $jadwalHariIni = Jadwal::where('id_kelas', $siswa->id_kelas)
            ->where('hari', now()->locale('id')->dayName)
            ->with(['guru.user'])
            ->get();

        $bulan = date('m');
        $tahun = date('Y');
        $persentaseKehadiran = $this->dbFunction->hitungPersentaseKehadiran($user->id_user, $bulan, $tahun);

        // Hitung total materi tersedia
        $totalMateri = Materi::where('id_kelas', $siswa->id_kelas)->count();
        
        // Hitung pengajuan izin yang belum disetujui (pending)
        $totalIzinPending = Izin::where('id_user', $user->id_user)
            ->where('status', 'pending')
            ->count();
        
        // Hitung jadwal hari ini
        $jadwalHariIniCount = $jadwalHariIni->count();

        $totalTugas = Tugas::where('id_kelas', $siswa->id_kelas)->count();
        $tugasSelesai = PengumpulanTugas::where('id_siswa', $siswa->id_siswa)->count();

        return view('siswa.dashboard', compact('jadwalHariIni', 'persentaseKehadiran', 'totalMateri', 'totalIzinPending', 'jadwalHariIniCount', 'totalTugas', 'tugasSelesai'));
    }

    public function profile()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        return view('siswa.profile', compact('siswa'));
    }

    public function roster()
    {
        $user = auth()->user();
        $siswa = $user->siswa;

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Get all jadwal for this student's class, grouped by day
        $jadwalPerHari = Jadwal::where('id_kelas', $siswa->id_kelas)
            ->with(['guru.user', 'kelas'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('hari');

        return view('siswa.roster', compact('jadwalPerHari', 'siswa'));
    }

    public function updateSemester(Request $request)
    {
        $request->validate([
            'semester' => 'required|string|in:X Semester Ganjil 2025/2026,XI Semester Ganjil 2025/2026,XII Semester Ganjil 2025/2026'
        ], [
            'semester.in' => 'Semester yang dipilih tidak valid. Hanya semester Ganjil 2025/2026 yang tersedia.'
        ]);

        try {
            $user = auth()->user();
            $siswa = $user->siswa;

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan.'
                ], 404);
            }

            $siswa->update([
                'semester' => $request->semester
            ]);

            // Log activity
            $this->logActivity->log(
                'ubah_semester',
                $user->id_user,
                "Mengubah semester menjadi {$request->semester}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Semester berhasil diubah. Halaman akan dimuat ulang.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewJadwal()
    {
        $siswa = auth()->user()->siswa;
        $jadwal = Jadwal::where('id_kelas', $siswa->id_kelas)
            ->with(['guru.user'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
            ->get()
            ->groupBy('hari');

        return view('siswa.jadwal', compact('jadwal'));
    }

    public function presensi()
    {
        $siswa = auth()->user()->siswa;
        $presensi = PresensiSiswa::where('id_siswa', $siswa->id_siswa)
            ->with(['jadwal.kelas', 'jadwal.guru.user'])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('siswa.presensi', compact('presensi'));
    }

    public function materi()
    {
        $siswa = auth()->user()->siswa;
        $materi = Materi::where('id_kelas', $siswa->id_kelas)
            ->with(['guru.user', 'kelas'])
            ->orderBy('uploaded_at', 'desc')
            ->paginate(20);

        return view('siswa.materi', compact('materi'));
    }

    public function downloadMateri($id)
    {
        $materi = Materi::findOrFail($id);
        $siswa = auth()->user()->siswa;

        if ($materi->id_kelas !== $siswa->id_kelas) {
            abort(403);
        }

        $this->logActivity->log('download_materi', auth()->user()->id_user, 'Download materi: ' . $materi->judul_materi);

        return response()->download(storage_path('app/public/materi/' . $materi->file_materi), $materi->file_materi);
    }

    public function tugas()
    {
        $siswa = auth()->user()->siswa;
        $tugas = Tugas::where('id_kelas', $siswa->id_kelas)
            ->with(['guru.user', 'kelas'])
            ->orderBy('deadline', 'desc')
            ->get();

        // Check which tugas already submitted
        $tugasWithStatus = $tugas->map(function($t) use ($siswa) {
            $pengumpulan = PengumpulanTugas::where('id_tugas', $t->id_tugas)
                ->where('id_siswa', $siswa->id_siswa)
                ->first();
            $t->sudah_mengumpulkan = $pengumpulan ? true : false;
            $t->pengumpulan_data = $pengumpulan;
            return $t;
        });

        return view('siswa.tugas.tugas-siswa-list', compact('tugasWithStatus'));
    }

    public function detailTugas($id)
    {
        $tugas = Tugas::with(['guru.user', 'kelas'])->findOrFail($id);
        $siswa = auth()->user()->siswa;

        if ($tugas->id_kelas !== $siswa->id_kelas) {
            abort(403);
        }

        $pengumpulan = PengumpulanTugas::where('id_tugas', $id)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();

        return view('siswa.tugas.detail', compact('tugas', 'pengumpulan'));
    }

    public function submitTugas(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar|max:10240',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $tugas = Tugas::findOrFail($id);
        $siswa = auth()->user()->siswa;

        if ($tugas->id_kelas !== $siswa->id_kelas) {
            abort(403);
        }

        // Check if already submitted
        $existing = PengumpulanTugas::where('id_tugas', $id)
            ->where('id_siswa', $siswa->id_siswa)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah mengumpulkan tugas ini sebelumnya');
        }

        // Upload file
        $file = $request->file('file');
        $filename = time() . '_' . $siswa->id_siswa . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('tugas_submissions', $filename, 'public');

        // Determine status (tepat_waktu or terlambat)
        $now = now();
        $deadline = \Carbon\Carbon::parse($tugas->deadline);
        $status = $now->lte($deadline) ? 'tepat_waktu' : 'terlambat';

        PengumpulanTugas::create([
            'id_tugas' => $id,
            'id_siswa' => $siswa->id_siswa,
            'file_jawaban' => $path,
            'keterangan' => $request->keterangan,
            'waktu_submit' => $now,
            'status' => $status,
        ]);

        $this->logActivity->log('submit_tugas', auth()->user()->id_user, 'Submit tugas: ' . $tugas->judul_tugas);

        return redirect()->route('siswa.tugas')->with('success', 'Tugas berhasil dikumpulkan');
    }

    public function izin()
    {
        $user = auth()->user();
        $siswa = $user->siswa;
        
        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }
        
        // Ambil semua izin yang pernah diajukan user ini
        $izinList = Izin::where('id_user', $user->id_user)
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return view('siswa.izin', compact('izinList'));
    }

    public function createIzin()
    {
        return view('siswa.izin-create');
    }

    public function storeIzin(Request $request)
    {
        $request->validate([
            'tipe' => 'required|in:sakit,izin',
            'tanggal' => 'required|date',
            'hari' => 'required|in:Minggu,Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'id_jadwal' => 'nullable|exists:jadwal,id_jadwal',
            'alasan' => 'required_if:tipe,izin|nullable|string|max:500',
            'bukti_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Get guru from jadwal if jadwal selected
        $id_guru = null;
        if ($request->id_jadwal) {
            $jadwal = Jadwal::find($request->id_jadwal);
            if ($jadwal) {
                $id_guru = $jadwal->id_guru;
            }
        }

        // CRITICAL: Set tipe dan alasan dengan benar
        $tipe = $request->input('tipe'); // Harus 'sakit' atau 'izin'
        $alasan = ($tipe === 'sakit') ? '-' : ($request->input('alasan') ?: '-');

        $data = [
            'id_user' => auth()->user()->id_user,
            'tipe' => $tipe, // PASTIKAN INI CORRECT!
            'id_jadwal' => $request->id_jadwal,
            'id_guru' => $id_guru,
            'tanggal' => $request->tanggal,
            'hari' => $request->hari,
            'alasan' => $alasan,
            'status' => 'pending',
        ];

        if ($request->hasFile('bukti_file')) {
            $file = $request->file('bukti_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('izin', $filename, 'public');
            $data['bukti_file'] = $filename;
        }

        $izin = Izin::create($data);
        $this->logActivity->log('submit_izin', auth()->user()->id_user, 'Mengajukan izin ' . $request->tipe . ' untuk tanggal: ' . $request->tanggal);

        return redirect()->route('siswa.izin')->with('success', 'Pengajuan izin berhasil dikirim.');
    }

    /**
     * Submit absen siswa untuk jadwal hari ini
     * POST /siswa/presensi/submit
     */
    public function submitAbsen(Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:hadir,izin,sakit',
                'keterangan' => 'nullable|string|max:255',
            ]);

            $user = auth()->user();
            $siswa = $user->siswa;

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i:s');
            $dayName = now()->format('l'); // Monday, Tuesday, etc
            $dayMapping = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];
            $hari = $dayMapping[$dayName] ?? null;

            // Cek jadwal yang sedang aktif (berdasarkan jam pelajaran)
            $jadwalHariIni = Jadwal::where('id_kelas', $siswa->id_kelas)
                ->where('hari', $hari)
                ->where('jam_mulai', '<=', $currentTime)
                ->where('jam_selesai', '>=', $currentTime)
                ->first();

            if (!$jadwalHariIni) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada jadwal pelajaran yang sedang aktif pada saat ini'
                ], 400);
            }

            // Cek apakah sudah pernah absen hari ini untuk jadwal ini
            $presensiExisting = PresensiSiswa::where('id_siswa', $siswa->id_siswa)
                ->where('id_jadwal', $jadwalHariIni->id_jadwal)
                ->where('tanggal', $today)
                ->first();

            if ($presensiExisting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah pernah absen untuk mata pelajaran ini hari ini. Status: ' . ucfirst($presensiExisting->status),
                    'existing_status' => $presensiExisting->status,
                    'existing_at' => $presensiExisting->created_at->format('H:i:s')
                ], 409);
            }

            // Submit absen
            $presensi = PresensiSiswa::create([
                'id_siswa' => $siswa->id_siswa,
                'id_jadwal' => $jadwalHariIni->id_jadwal,
                'tanggal' => $today,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'status_verifikasi' => 'pending',
                'diinput_oleh_tipe' => 'siswa',
            ]);

            // Log activity
            $this->logActivity->log(
                'presensi_siswa_submit',
                $user->id_user,
                "Submit absen: {$request->status} untuk jadwal {$jadwalHariIni->id_jadwal}"
            );

            return response()->json([
                'success' => true,
                'message' => 'Absen berhasil dikirim! Status: ' . ucfirst($request->status),
                'data' => [
                    'id_presensi_siswa' => $presensi->id_presensi_siswa,
                    'status' => $presensi->status,
                    'status_verifikasi' => $presensi->status_verifikasi,
                    'tanggal' => \Carbon\Carbon::parse($presensi->tanggal)->format('d-m-Y'),
                    'jam_submit' => $presensi->created_at->format('H:i:s')
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lihat status absen siswa
     * GET /siswa/presensi/status
     */
    public function statusAbsenHariIni()
    {
        try {
            $user = auth()->user();
            $siswa = $user->siswa;

            if (!$siswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ], 404);
            }

            $today = now()->format('Y-m-d');
            $currentTime = now()->format('H:i:s');
            $dayName = now()->format('l'); // Monday, Tuesday, etc
            $dayMapping = [
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
                'Sunday' => 'Minggu'
            ];
            $hari = $dayMapping[$dayName] ?? null;

            // Cek jadwal yang sedang aktif (berdasarkan jam pelajaran)
            $jadwalHariIni = Jadwal::where('id_kelas', $siswa->id_kelas)
                ->where('hari', $hari)
                ->where('jam_mulai', '<=', $currentTime)
                ->where('jam_selesai', '>=', $currentTime)
                ->first();

            if (!$jadwalHariIni) {
                return response()->json([
                    'success' => true,
                    'sudah_absen' => false,
                    'message' => 'Tidak ada jadwal pelajaran yang sedang aktif pada saat ini',
                    'data' => null
                ]);
            }

            // Cek absen hari ini
            $absenHariIni = PresensiSiswa::where('id_siswa', $siswa->id_siswa)
                ->where('id_jadwal', $jadwalHariIni->id_jadwal)
                ->where('tanggal', $today)
                ->with('jadwal.guru.user')
                ->first();

            if ($absenHariIni) {
                return response()->json([
                    'success' => true,
                    'sudah_absen' => true,
                    'message' => 'Anda sudah melakukan absen hari ini',
                    'data' => [
                        'id_presensi_siswa' => $absenHariIni->id_presensi_siswa,
                        'status' => $absenHariIni->status,
                        'status_verifikasi' => $absenHariIni->status_verifikasi,
                        'mata_pelajaran' => $absenHariIni->jadwal->nama_matpel ?? 'N/A',
                        'guru' => $absenHariIni->jadwal->guru->user->nama_lengkap ?? 'N/A',
                        'jam_submit' => $absenHariIni->created_at->format('H:i:s'),
                        'verifikasi_at' => $absenHariIni->diverifikasi_at?->format('d-m-Y H:i:s')
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'sudah_absen' => false,
                'message' => 'Anda belum melakukan absen',
                'data' => [
                    'jadwal' => $jadwalHariIni->id_jadwal,
                    'nama_matpel' => $jadwalHariIni->nama_matpel ?? 'N/A',
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show form untuk ajukan izin
     * GET /siswa/izin/buat
     */
    public function ajukanIzin()
    {
        return view('siswa.izin-create');
    }

    /**
     * Submit ajukan izin
     * POST /siswa/izin/submit
     */
    public function submitAjukanIzin(Request $request)
    {
        try {
            $request->validate([
                'tipe' => 'required|in:sakit,izin',
                'tanggal' => 'required|date|after_or_equal:today',
                'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
                'id_jadwal' => 'required|exists:jadwal_pelajaran,id_jadwal',
                'alasan' => $request->tipe === 'izin' ? 'required|string|min:10|max:500' : 'nullable|string',
                'bukti_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ], [
                'bukti_file.mimes' => 'Format file harus PDF, JPG, atau PNG',
                'bukti_file.max' => 'Ukuran file maksimal 5 MB',
                'tanggal.required' => 'Tanggal izin wajib diisi',
                'tanggal.after_or_equal' => 'Tanggal izin tidak boleh tanggal yang sudah lewat',
                'hari.required' => 'Hari pelajaran wajib dipilih',
                'id_jadwal.required' => 'Jam pelajaran wajib dipilih',
                'id_jadwal.exists' => 'Jadwal tidak valid',
                'alasan.required' => 'Alasan izin wajib diisi ketika tipe "Izin"',
                'alasan.min' => 'Alasan izin minimal 10 karakter',
            ]);

            $user = auth()->user();
            $siswa = $user->siswa;

            if (!$siswa) {
                return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
            }

            // Get jadwal to extract id_guru
            $jadwal = Jadwal::findOrFail($request->id_jadwal);

            // Upload file bukti (opsional)
            $path = null;
            if ($request->hasFile('bukti_file')) {
                $file = $request->file('bukti_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('izin', $filename, 'public');
            }

            // Buat record izin dengan id_guru dari jadwal
            $izin = Izin::create([
                'id_user' => $user->id_user,
                'id_guru' => $jadwal->id_guru,
                'id_jadwal' => $request->id_jadwal,
                'tipe' => $request->tipe, // CRITICAL: Simpan field tipe!
                'tanggal' => $request->tanggal,
                'hari' => $request->hari, // Simpan field hari juga
                'alasan' => $request->tipe === 'sakit'
                    ? '-'
                    : $request->alasan,
                'bukti_file' => $path,
                'status' => 'pending', // Set status awal
            ]);

            // Log activity
            $this->logActivity->log(
                'ajukan_izin',
                $user->id_user,
                "Mengajukan izin ({$request->tipe}) untuk tanggal {$request->tanggal} ke guru {$jadwal->guru->user->nama}"
            );

            return redirect()->route('siswa.dashboard')->with('success', 'Izin berhasil dikirim ke guru yang bersangkutan.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function getJadwal(Request $request)
    {
        try {
            $request->validate([
                'id_kelas' => 'required|exists:kelas,id_kelas',
                'hari' => 'required|in:Minggu,Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            ]);

            $jadwal = Jadwal::where('id_kelas', $request->id_kelas)
                ->where('hari', $request->hari)
                ->with('guru.user')
                ->orderBy('jam_mulai')
                ->get()
                ->map(function($item) {
                    return [
                        'id_jadwal' => $item->id_jadwal,
                        'mata_pelajaran' => $item->mata_pelajaran,
                        'jam_mulai' => substr($item->jam_mulai, 0, 5), // HH:MM
                        'jam_selesai' => substr($item->jam_selesai, 0, 5), // HH:MM
                        'guru_nama' => $item->guru && $item->guru->user ? $item->guru->user->nama_lengkap : 'Guru tidak ditemukan',
                    ];
                });

            return response()->json($jadwal);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'message' => $e->getMessage(),
                'data' => []
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function kegiatan()
    {
        // Auto-update status kegiatan berdasarkan waktu
        $this->autoUpdateStatusKegiatan();
        
        $filter = request('filter', 'upcoming'); // 'upcoming' atau 'history'
        
        $query = Kegiatan::with('pembuatKegiatan');
        
        if ($filter === 'history') {
            $query->where('status', 'completed');
            $orderBy = 'desc';
        } else {
            $query->whereIn('status', ['planned', 'ongoing']);
            $orderBy = 'asc';
        }
        
        $kegiatan = $query->orderBy('tanggal_mulai', $orderBy)->paginate(20);
        
        return view('siswa.kegiatan.index', compact('kegiatan', 'filter'));
    }

    private function autoUpdateStatusKegiatan()
    {
        $now = now();
        
        // Update status ke 'completed' jika waktu selesai sudah lewat
        Kegiatan::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->where(function($query) use ($now) {
                $query->where('tanggal_selesai', '<', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_selesai', '=', $now->format('Y-m-d'))
                          ->where('waktu_selesai', '<', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'completed']);
        
        // Update status ke 'ongoing' jika waktu mulai sudah tiba tapi belum selesai
        Kegiatan::where('status', 'planned')
            ->where(function($query) use ($now) {
                $query->where('tanggal_mulai', '<', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_mulai', '=', $now->format('Y-m-d'))
                          ->where('waktu_mulai', '<=', $now->format('H:i:s'));
                    });
            })
            ->where(function($query) use ($now) {
                $query->where('tanggal_selesai', '>', $now->format('Y-m-d'))
                    ->orWhere(function($q) use ($now) {
                        $q->where('tanggal_selesai', '=', $now->format('Y-m-d'))
                          ->where('waktu_selesai', '>=', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'ongoing']);
    }

    public function detailKegiatan($id)
    {
        $kegiatan = Kegiatan::with('pembuatKegiatan')->findOrFail($id);
        return view('siswa.kegiatan.detail', compact('kegiatan'));
    }
}
