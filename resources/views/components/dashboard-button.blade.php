@php
    $user = auth()->user();
    $role = $user->role;
    
    $dashboardRoutes = [
        'admin' => 'admin.dashboard',
        'guru' => 'guru.dashboard',
        'siswa' => 'siswa.dashboard',
        'kepala_sekolah' => 'kepala_sekolah.dashboard',
        'pembina' => 'pembina.dashboard'
    ];
    
    $dashboardRoute = $dashboardRoutes[$role] ?? null;
    $dashboardUrl = $dashboardRoute ? route($dashboardRoute) : '#';
@endphp

@if($dashboardRoute)
    <a href="{{ $dashboardUrl }}" 
       style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; border-radius: 0.5rem; text-decoration: none; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease;"
       onmouseover="this.style.boxShadow='0 4px 6px rgba(0,0,0,0.1)'; this.style.transform='translateY(-2px)';"
       onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">
        <i class="fas fa-home"></i> Dashboard
    </a>
@endif
