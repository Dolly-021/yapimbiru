@extends('layouts.dashboard')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Detail Tugas</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Lihat detail tugas dan pengumpulan siswa</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <form action="{{ route('guru.tugas.delete', $tugas->id_tugas) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus tugas ini? Semua pengumpulan siswa ({{ $tugas->pengumpulan->count() }} siswa) juga akan dihapus.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-trash"></i>
                    Hapus Tugas
                </button>
            </form>
            <a href="{{ route('guru.tugas') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <x-dashboard-button />
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d1fae5; border: 1px solid #6ee7b7; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center;">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                <strong>{{ session('success') }}</strong>
            </div>
        </div>
    @endif

    <!-- Info Tugas -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 2rem; margin-bottom: 2rem;">
        <div style="border-left: 4px solid #0369a1; padding-left: 1rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">
                {{ $tugas->judul_tugas }}
            </h3>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <i class="fas fa-school"></i>
                    <span><strong>Kelas:</strong> {{ $tugas->kelas->nama_kelas }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <i class="fas fa-calendar-alt"></i>
                    <span><strong>Deadline:</strong> {{ \Carbon\Carbon::parse($tugas->deadline)->format('d M Y, H:i') }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #64748b;">
                    <i class="fas fa-users"></i>
                    <span><strong>Pengumpulan:</strong> {{ $tugas->pengumpulan->count() }} siswa</span>
                </div>
            </div>
        </div>

        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 0.5rem; border: 1px solid #e2e8f0;">
            <h4 style="font-weight: 600; color: #334155; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-file-alt"></i>
                Deskripsi & Instruksi
            </h4>
            <p style="color: #475569; line-height: 1.7; white-space: pre-wrap;">{{ $tugas->deskripsi }}</p>
        </div>
    </div>

    <!-- Daftar Pengumpulan -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%); color: white; padding: 1.25rem 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-clipboard-check"></i>
                Daftar Pengumpulan Siswa
            </h3>
        </div>

        @if($tugas->pengumpulan->isEmpty())
            <div style="padding: 3rem; text-align: center; color: #94a3b8;">
                <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                <p style="font-weight: 500;">Belum ada siswa yang mengumpulkan tugas</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: #f1f5f9;">
                        <tr>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">No</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Nama Siswa</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">NIS</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Waktu Pengumpulan</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">File</th>
                            <th style="padding: 1rem; text-align: left; font-weight: 600; color: #475569;">Nilai</th>
                            <th style="padding: 1rem; text-align: center; font-weight: 600; color: #475569;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tugas->pengumpulan as $index => $p)
                            <tr style="border-bottom: 1px solid #e2e8f0;">
                                <td style="padding: 1rem;">{{ $index + 1 }}</td>
                                <td style="padding: 1rem;">
                                    <strong>{{ $p->siswa->user->nama_lengkap ?? '-' }}</strong>
                                </td>
                                <td style="padding: 1rem;">{{ $p->siswa->nis ?? '-' }}</td>
                                <td style="padding: 1rem;">
                                    {{ \Carbon\Carbon::parse($p->waktu_submit)->format('d M Y, H:i') }}
                                    @if($p->status === 'terlambat')
                                        <span style="background: #fee2e2; color: #991b1b; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; margin-left: 0.5rem;">Terlambat</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($p->file_jawaban)
                                        <a href="{{ asset('storage/' . $p->file_jawaban) }}" target="_blank" class="btn btn-sm btn-info" style="padding: 0.25rem 0.75rem;">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @else
                                        <span style="color: #94a3b8;">-</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem;">
                                    @if($p->nilai)
                                        <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600;">
                                            {{ $p->nilai }}
                                        </span>
                                    @else
                                        <span style="color: #94a3b8;">Belum dinilai</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <button onclick="openNilaiModal({{ $p->id_pengumpulan }}, '{{ $p->siswa->user->nama_lengkap }}', {{ $p->nilai ?? 0 }}, '{{ $p->feedback_guru ?? '' }}')" class="btn btn-sm btn-primary" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-edit"></i> {{ $p->nilai ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Modal Beri Nilai -->
<div id="nilaiModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 0.75rem; width: 90%; max-width: 500px; padding: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-bottom: 1.5rem;">
            <i class="fas fa-star"></i> Beri Nilai
        </h3>
        
        <form id="nilaiForm" method="POST">
            @csrf
            <input type="hidden" id="modalIdPengumpulan" name="id_pengumpulan">
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">Nama Siswa</label>
                <p id="modalNamaSiswa" style="color: #64748b; font-size: 0.95rem;"></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="modalNilai" style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                    Nilai (0-100) <span style="color: #dc2626;">*</span>
                </label>
                <input type="number" id="modalNilai" name="nilai" min="0" max="100" required style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="modalFeedback" style="display: block; font-weight: 600; color: #334155; margin-bottom: 0.5rem;">
                    Feedback (Opsional)
                </label>
                <textarea id="modalFeedback" name="feedback" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-family: inherit;" placeholder="Berikan komentar atau saran..."></textarea>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeNilaiModal()" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openNilaiModal(idPengumpulan, namaSiswa, nilai, feedback) {
    document.getElementById('modalIdPengumpulan').value = idPengumpulan;
    document.getElementById('modalNamaSiswa').textContent = namaSiswa;
    document.getElementById('modalNilai').value = nilai || '';
    document.getElementById('modalFeedback').value = feedback || '';
    
    const form = document.getElementById('nilaiForm');
    form.action = `/guru/tugas/${idPengumpulan}/nilai`;
    
    document.getElementById('nilaiModal').style.display = 'flex';
}

function closeNilaiModal() {
    document.getElementById('nilaiModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('nilaiModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeNilaiModal();
    }
});
</script>
@endsection
