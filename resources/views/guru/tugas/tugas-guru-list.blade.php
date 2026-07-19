@extends('layouts.dashboard')

@section('content')
<div class="content-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h2 class="section-title">Daftar Tugas</h2>
            <p style="color: #718096; margin-top: 0.5rem;">Kelola tugas yang sudah dibuat dan lihat pengumpulan siswa</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('guru.tugas.create') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-plus"></i>
                Buat Tugas Baru
            </a>
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

    @if($tugas->isEmpty())
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <p>Belum ada tugas yang dibuat</p>
        </div>
    @else
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: linear-gradient(135deg, #0369a1 0%, #06b6d4 50%, #14b8a6 100%); color: white;">
                    <tr>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">No</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Judul Tugas</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Kelas</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Deadline</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Pengumpulan</th>
                        <th style="padding: 1rem; text-align: center; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tugas as $index => $t)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 1rem;">{{ $index + 1 }}</td>
                            <td style="padding: 1rem;">
                                <strong>{{ $t->judul_tugas }}</strong>
                            </td>
                            <td style="padding: 1rem;">{{ $t->kelas->nama_kelas ?? '-' }}</td>
                            <td style="padding: 1rem;">
                                {{ \Carbon\Carbon::parse($t->deadline)->format('d M Y, H:i') }}
                            </td>
                            <td style="padding: 1rem;">
                                <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600;">
                                    {{ $t->pengumpulan->count() }} siswa
                                </span>
                            </td>
                            <td style="padding: 1rem; text-align: center;">
                                <a href="{{ route('guru.tugas.detail', $t->id_tugas) }}" class="btn btn-sm btn-info" style="padding: 0.5rem 1rem; margin-right: 0.5rem;">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <form action="{{ route('guru.tugas.delete', $t->id_tugas) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus tugas ini? Semua pengumpulan siswa juga akan dihapus.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" style="padding: 0.5rem 1rem;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
