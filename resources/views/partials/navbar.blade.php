<div class="navbar">
    <div class="navbar-brand">
        <img src="{{ asset('images/yapim.png') }}" alt="Logo" style="height: 40px; width: auto; border-radius: 8px; cursor: pointer;" 
             onclick="goToDashboard()" title="Kembali">
        <a href="javascript:void(0);" onclick="goToDashboard()" style="color: white; text-decoration: none; cursor: pointer;">
            SMK YAPIM BIRU-BIRU
        </a>
    </div>
    <div class="user-info">
        <div class="user-name">
            <span>{{ auth()->user()->nama_lengkap }}</span>
            <span class="role-badge">{{ strtoupper(auth()->user()->role) }}</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </div>
</div>

<script>
function goToDashboard() {
    const role = '{{ auth()->user()->role }}';
    let dashboardUrl = '/';
    
    switch(role) {
        case 'admin':
            dashboardUrl = '{{ route("admin.dashboard") }}';
            break;
        case 'guru':
            dashboardUrl = '{{ route("guru.dashboard") }}';
            break;
        case 'siswa':
            dashboardUrl = '{{ route("siswa.dashboard") }}';
            break;
        case 'kepala_sekolah':
            dashboardUrl = '{{ route("kepala_sekolah.dashboard") }}';
            break;
        case 'pembina':
            dashboardUrl = '{{ route("pembina.dashboard") }}';
            break;
        default:
            dashboardUrl = '/';
    }
    
    window.location.href = dashboardUrl;
}
</script>

