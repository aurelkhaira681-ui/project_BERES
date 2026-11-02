<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'login_as' => ['required', 'in:siswa,admin'],
        ]);

        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => $credentials['login_as'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email, kata sandi, atau jenis akun tidak sesuai.',
            ]);
        }

        $request->session()->regenerate();

        return $request->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'string', 'max:20', 'unique:users,nisn'],
            'kelas' => ['required', 'string', 'max:50'],
            'no_presensi' => ['required', 'string', 'max:10'],
            'konsentrasi_keahlian' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'role' => 'siswa',
            'name' => $data['name'],
            'nisn' => $data['nisn'],
            'kelas' => $data['kelas'],
            'no_presensi' => $data['no_presensi'],
            'konsentrasi_keahlian' => $data['konsentrasi_keahlian'],
            'no_hp' => $data['no_hp'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        return redirect()->route('siswa.dashboard');
    }

    // ===== Pendaftaran Admin / Petugas (wajib kode registrasi dari sekolah) =====
    public function showRegisterPetugas()
    {
        return view('auth.register-petugas', [
            'aktif' => filled(config('beres.petugas_registration_code')),
        ]);
    }

    public function registerPetugas(Request $request)
    {
        $kodeBenar = config('beres.petugas_registration_code');

        abort_if(blank($kodeBenar), 403, 'Pendaftaran petugas belum diaktifkan.');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'kode_registrasi' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!hash_equals((string) $kodeBenar, (string) $data['kode_registrasi'])) {
            throw ValidationException::withMessages([
                'kode_registrasi' => 'Kode registrasi petugas tidak valid.',
            ]);
        }

        $user = User::create([
            'role' => 'admin',
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    // ===== Lupa kata sandi (verifikasi identitas, tanpa email) =====
    public function showForgot()
    {
        return view('auth.lupa-sandi', [
            'petugasAktif' => filled(config('beres.petugas_registration_code')),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'in:siswa,admin'],
            'email' => ['required', 'email'],
            'nisn' => ['required_if:role,siswa', 'nullable', 'string'],
            'kode_registrasi' => ['required_if:role,admin', 'nullable', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::where('email', $data['email'])->where('role', $data['role'])->first();

        if ($data['role'] === 'siswa') {
            $valid = $user && filled($user->nisn) && hash_equals((string) $user->nisn, (string) $data['nisn']);
        } else {
            $kode = config('beres.petugas_registration_code');
            $valid = $user && filled($kode) && hash_equals((string) $kode, (string) $data['kode_registrasi']);
        }

        if (!$valid) {
            throw ValidationException::withMessages([
                'email' => 'Data yang dimasukkan tidak cocok. Periksa kembali email dan ' . ($data['role'] === 'siswa' ? 'NISN' : 'kode registrasi') . '.',
            ]);
        }

        $user->update(['password' => $data['password']]); // otomatis di-hash oleh cast model

        return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}