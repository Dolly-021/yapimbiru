<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $kelas = \App\Models\Kelas::orderBy('tingkat', 'asc')->orderBy('nama_kelas', 'asc')->get();
        return view('auth.register', compact('kelas'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi berdasarkan role
        $validationRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:siswa,guru'],
        ];

        // Validasi tambahan untuk siswa
        if ($request->role === 'siswa') {
            $validationRules['id_kelas'] = ['required', 'exists:kelas,id_kelas'];
            $validationRules['nisn'] = ['required', 'string', 'max:20', 'unique:siswa,nis'];
        }

        // Validasi tambahan untuk guru
        if ($request->role === 'guru') {
            $validationRules['nip'] = ['required', 'string', 'max:30', 'unique:guru,nip'];
        }

        $request->validate($validationRules);

        $user = User::create([
            'username' => $request->email,
            'nama_lengkap' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status_aktif' => true,
        ]);

        // Jika role siswa, buat record di tabel siswa
        if ($request->role === 'siswa') {
            Siswa::create([
                'id_user' => $user->id_user,
                'nis' => $request->nisn,
                'id_kelas' => $request->id_kelas,
            ]);
        }

        // Jika role guru, buat record di tabel guru
        if ($request->role === 'guru') {
            Guru::create([
                'id_user' => $user->id_user,
                'nip' => $request->nip,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
