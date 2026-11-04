<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiswaTerdaftar;
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
        $request->validate([
            'identitas' => ['required', 'string'],
            'password' => ['required'],
            'login_as' => ['required', 'in:siswa,admin'],
        ]);

        $identitas = trim($request->input('identitas'));
        $remember = $request->boolean('remember');

        if ($request->input('login_as') === 'siswa') {
            // Siswa masuk dengan NISN. NISN yang tidak ada di daftar resmi sekolah ditolak.
            $nisn = SiswaTerdaftar::normalisasi($identitas);

            $berhasil = SiswaTerdaftar::where('nisn', $nisn)->exists()
                && Auth::attempt(['nisn' => $nisn, 'password' => $request->input('password'), 'role' => 'siswa'], $remember);

            $pesan = __('NISN atau kata sandi tidak sesuai, atau NISN belum terdaftar di sekolah.');
        } else {
            $berhasil = Auth::attempt(['email' => $identitas, 'password' => $request->input('password'), 'role' => 'admin'], $remember);
            $pesan = __('Email atau kata sandi tidak sesuai.');
        }

        if (! $berhasil) {
            throw ValidationException::withMessages(['identitas' => $pesan]);
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
        // Rapikan NISN dulu (angka saja, 10 digit) supaya "86944390" sama dengan "0086944390"
        $request->merge(['nisn' => SiswaTerdaftar::normalisasi($request->input('nisn'))]);

        $data = $request->validate([
            'nisn' => ['required', 'digits:10', 'exists:siswa_terdaftar,nisn', 'unique:users,nisn'],
            'name' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:50'],
            'no_presensi' => ['required', 'string', 'max:10'],
            'konsentrasi_keahlian' => ['required', 'string', 'max:100'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'nisn.exists' => __('NISN tidak terdaftar di data siswa sekolah. Periksa kembali atau hubungi petugas.'),
            'nisn.unique' => __('NISN ini sudah dipakai untuk mendaftar. Silakan masuk, atau gunakan Lupa kata sandi.'),
            'nisn.digits' => __('NISN harus 10 digit angka.'),
        ]);

        // Nama & kelas diambil dari data resmi sekolah, bukan dari isian siswa
        $resmi = SiswaTerdaftar::where('nisn', $data['nisn'])->firstOrFail();

        $user = User::create([
            'role' => 'siswa',
            'name' => $resmi->nama,
            'nisn' => $resmi->nisn,
            'kelas' => $resmi->kelas,
            'no_presensi' => $data['no_presensi'],
            'konsentrasi_keahlian' => $data['konsentrasi_keahlian'],
            'no_hp' => $data['no_hp'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

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

        abort_if(blank($kodeBenar), 403, __('Pendaftaran petugas belum diaktifkan.'));

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'kode_registrasi' => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (!hash_equals((string) $kodeBenar, (string) $data['kode_registrasi'])) {
            throw ValidationException::withMessages([
                'kode_registrasi' => __('Kode registrasi petugas tidak valid.'),
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
            $valid = $user && filled($user->nisn) && hash_equals((string) $user->nisn, SiswaTerdaftar::normalisasi($data['nisn']));
        } else {
            $kode = config('beres.petugas_registration_code');
            $valid = $user && filled($kode) && hash_equals((string) $kode, (string) $data['kode_registrasi']);
        }

        if (!$valid) {
            throw ValidationException::withMessages([
                'email' => __('Data yang dimasukkan tidak cocok. Periksa kembali email dan :field.', [
                    'field' => $data['role'] === 'siswa' ? 'NISN' : __('kode registrasi'),
                ]),
            ]);
        }

        $user->update(['password' => $data['password']]); // otomatis di-hash oleh cast model

        return redirect()->route('login')->with('status', __('Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}