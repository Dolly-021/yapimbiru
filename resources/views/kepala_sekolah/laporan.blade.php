@extends('layouts.dashboard')

@section('content')
<style>
    .page-title { font-size: 1.5rem; font-weight: 700; color: #2d3748; margin-bottom: 0.5rem; }
    .page-desc { color: #718096; margin-bottom: 2rem; }
    .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
    .alert-error { background: #fee2e2; border-left: 4px solid #dc2626; color: #7f1d1d; }
    .alert-success { background: #dcfce7; border-left: 4px solid #16a34a; color: #166534; }
    .laporan-card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 1.5rem; overflow: hidden; }
    .laporan-header { padding: 1.5rem; color: white; }
    .laporan-header.pending { background: linear-gradient(135deg, #ea580c 0%, #f97316 100%); }
    .laporan-header.reviewed { background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); }
    .laporan-header.approved { background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%); }
    .laporan-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; }
    .laporan-info-label { font-size: 0.85rem; opacity: 0.9; }
    .laporan-info-value { font-weight: 600; font-size: 1rem; margin-bottom: 0.5rem; }
    .laporan-body { padding: 1.5rem; }
    .section-content { background: #f7fafc; padding: 1rem; border-radius: 8px; color: #2d3748; line-height: 1.6; margin-bottom: 1rem; }
    .section-title { font-weight: 600; color: #4a5568; margin-bottom: 0.5rem; font-size: 0.9rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem; }
    .box-blue { background: #dbeafe; padding: 1rem; border-radius: 8px; }
    .box-orange { background: #fed7aa; padding: 1rem; border-radius: 8px; }
    .box-label { font-size: 0.85rem; font-weight: 600; color: #4a5568; margin-bottom: 0.5rem; }
    .box-text { color: #2d3748; line-height: 1.5; }
    .form-group { margin-bottom: 1rem; }
    .form-label { font-size: 0.9rem; font-weight: 600; color: #2d3748; margin-bottom: 0.5rem; }
    .form-control { width: 100%; padding: 0.75rem; border: 1px solid #cbd5e0; border-radius: 8px; font-size: 0.9rem; font-family: inherit; }
    .btn-submit { width: 100%; background: linear-gradient(135deg, #0369a1 0%, #06b6d4 100%); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
    .btn-submit:hover { opacity: 0.9; transform: translateY(-1px); }
</style>

<div style="max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div class="page-title">Laporan Aktivitas</div>
    <div class="page-desc">Review laporan aktivitas pembina dan guru</div>

    <!-- Alerts -->
    @if($errors->any())
        <div class="alert alert-error">
            <strong>Error:</strong>
            <ul style="margin-top: 0.5rem;">
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <strong>✓ {{ session('success') }}</strong>
        </div>
    @endif

    <!-- Laporan List -->
    @forelse($laporan as $item)
        <div class="laporan-card">
            <div class="laporan-header {{ $item->status == 'pending' ? 'pending' : ($item->status == 'approved' ? 'approved' : 'reviewed') }}">
                <div class="laporan-grid">
                    <div>
                        <div class="laporan-info-label">Pembina</div>
                        <div class="laporan-info-value">{{ $item->pembina->user->nama_lengkap ?? $item->pembina->user->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="laporan-info-label">Periode</div>
                        <div class="laporan-info-value">{{ \Carbon\Carbon::createFromDate($item->periode_tahun, $item->periode_bulan, 1)->format('F Y') }}</div>
                    </div>
                    <div>
                        <div class="laporan-info-label">Status</div>
                        <div class="laporan-info-value">{{ ucfirst(str_replace('_', ' ', $item->status)) }}</div>
                    </div>
                </div>
            </div>

            <div class="laporan-body">
                <!-- Ringkasan -->
                <div style="margin-bottom: 1rem;">
                    <div class="section-title">Ringkasan</div>
                    <div class="section-content">{{ Str::limit($item->ringkasan_aktivitas, 300) }}</div>
                </div>

                <!-- Capaian & Kendala -->
                <div class="grid-2">
                    <div class="box-blue">
                        <div class="box-label">Capaian</div>
                        <div class="box-text">{{ Str::limit($item->capaian, 150) }}</div>
                    </div>
                    <div class="box-orange">
                        <div class="box-label">Kendala</div>
                        <div class="box-text">{{ Str::limit($item->kendala, 150) }}</div>
                    </div>
                </div>

                <!-- Catatan Kepsek (if exists) -->
                @if($item->catatan_kepsek)
                    <div style="background: #f3e8ff; border-left: 4px solid #a855f7; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <div class="section-title" style="color: #6d28d9;">Catatan Kepala Sekolah</div>
                        <div style="color: #5b21b6;">{{ $item->catatan_kepsek }}</div>
                    </div>
                @endif

                <!-- Action -->
                @if($item->status == 'pending')
                    <form method="POST" action="{{ route('kepala_sekolah.laporan.review', $item->id_laporan) }}" style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Catatan Kepala Sekolah</label>
                            <textarea name="catatan_kepsek" rows="3" class="form-control" placeholder="Berikan catatan atau masukan..."></textarea>
                        </div>

                        <button type="submit" class="btn-submit">Proses Laporan</button>
                    </form>
                @else
                    <div style="background: #f7fafc; padding: 1rem; border-radius: 8px; border-left: 4px solid #cbd5e0; font-size: 0.9rem; color: #4a5568;">
                        Laporan sudah diproses pada {{ $item->reviewed_at?->format('d M Y H:i') ?? 'N/A' }}
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div style="background: #dbeafe; border-left: 4px solid #0369a1; padding: 2rem; border-radius: 8px; text-align: center;">
            <p style="color: #1e3a8a; font-weight: 600;">Tidak ada laporan aktivitas</p>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($laporan->hasPages())
        <div style="margin-top: 2rem;">
            {{ $laporan->links() }}
        </div>
    @endif
</div>
@endsection