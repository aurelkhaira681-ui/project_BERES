<?php

namespace Tests\Feature;

use App\Models\Aduan;
use App\Models\KategoriKerusakan;
use App\Models\Lokasi;
use App\Models\Teknisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AlurAduanTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(): array
    {
        $admin = User::create(['role' => 'admin', 'name' => 'Admin', 'email' => 'admin@test.id', 'password' => 'password']);
        $siswa = User::create(['role' => 'siswa', 'name' => 'Siswa', 'email' => 's@test.id', 'password' => 'password', 'nisn' => '0000000001', 'kelas' => 'XII RPL A']);
        $teknisi = Teknisi::create(['nama' => 'Pak Umum', 'jurusan' => Teknisi::BIDANG_SANITASI, 'aktif' => true]);
        $kategori = KategoriKerusakan::create(['nama' => 'Elektronik', 'icon' => '💡']);
        $lokasi = Lokasi::create(['nama_ruangan' => 'Rg 1.01', 'kelompok' => 'Ruang Kelas', 'lantai' => 'Lantai 1']);

        return compact('admin', 'siswa', 'teknisi', 'kategori', 'lokasi');
    }

    public function test_siswa_bisa_membuat_aduan_dan_teknisi_ditugaskan_otomatis(): void
    {
        Storage::fake('public');
        ['siswa' => $siswa, 'kategori' => $k, 'lokasi' => $l, 'teknisi' => $t] = $this->siapkan();

        $this->actingAs($siswa)->post(route('siswa.aduan.store'), [
            'kategori_kerusakan_id' => $k->id,
            'lokasi_id' => $l->id,
            'tanggal_kejadian' => now()->toDateString(),
            'deskripsi' => 'Lampu kelas mati total sejak pagi.',
            'foto' => [UploadedFile::fake()->image('bukti.jpg')],
        ])->assertRedirect();

        $aduan = Aduan::first();
        $this->assertSame('pending', $aduan->status);
        $this->assertSame($t->id, $aduan->teknisi_id);
        $this->assertCount(1, $aduan->foto);
    }

    public function test_admin_menyelesaikan_aduan_dengan_foto_hasil_perbaikan(): void
    {
        Storage::fake('public');
        ['admin' => $admin, 'siswa' => $siswa, 'kategori' => $k, 'lokasi' => $l, 'teknisi' => $t] = $this->siapkan();

        $aduan = $siswa->aduan()->create([
            'kategori_kerusakan_id' => $k->id, 'lokasi_id' => $l->id, 'teknisi_id' => $t->id,
            'tanggal_kejadian' => now(), 'deskripsi' => 'Lampu kelas mati total.', 'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('admin.aduan.setujui', $aduan))->assertRedirect();
        $this->assertSame('approved', $aduan->fresh()->status);

        $this->actingAs($admin)->post(route('admin.aduan.selesai', $aduan), [
            'catatan_perbaikan' => 'Lampu diganti baru.',
            'foto_selesai' => [UploadedFile::fake()->image('sesudah.jpg')],
        ])->assertRedirect();

        $aduan = $aduan->fresh();
        $this->assertSame('selesai', $aduan->status);
        $this->assertSame('Lampu diganti baru.', $aduan->catatan_perbaikan);
        $this->assertCount(1, $aduan->foto_selesai);
        Storage::disk('public')->assertExists($aduan->foto_selesai[0]);

        // Halaman detail siswa menampilkan hasil perbaikan + langkah teknisi
        $this->actingAs($siswa)->get(route('siswa.aduan.show', $aduan))
            ->assertOk()
            ->assertSee('Hasil Perbaikan')
            ->assertSee('Lampu diganti baru.')
            ->assertSee('Teknisi Menangani')
            ->assertSee('Pak Umum');

        // Halaman detail admin juga
        $this->actingAs($admin)->get(route('admin.aduan.show', $aduan))
            ->assertOk()->assertSee('Hasil Perbaikan');
    }

    public function test_selesai_tanpa_foto_tetap_berhasil(): void
    {
        ['admin' => $admin, 'siswa' => $siswa, 'kategori' => $k, 'lokasi' => $l, 'teknisi' => $t] = $this->siapkan();

        $aduan = $siswa->aduan()->create([
            'kategori_kerusakan_id' => $k->id, 'lokasi_id' => $l->id, 'teknisi_id' => $t->id,
            'tanggal_kejadian' => now(), 'deskripsi' => 'Kran bocor di toilet.', 'status' => 'approved',
        ]);

        $this->actingAs($admin)->post(route('admin.aduan.selesai', $aduan))->assertRedirect();

        $this->assertSame('selesai', $aduan->fresh()->status);
        $this->assertSame([], $aduan->fresh()->foto_selesai);
    }

    public function test_file_bukan_gambar_ditolak(): void
    {
        ['admin' => $admin, 'siswa' => $siswa, 'kategori' => $k, 'lokasi' => $l, 'teknisi' => $t] = $this->siapkan();

        $aduan = $siswa->aduan()->create([
            'kategori_kerusakan_id' => $k->id, 'lokasi_id' => $l->id, 'teknisi_id' => $t->id,
            'tanggal_kejadian' => now(), 'deskripsi' => 'Kran bocor di toilet.', 'status' => 'approved',
        ]);

        $this->actingAs($admin)->post(route('admin.aduan.selesai', $aduan), [
            'foto_selesai' => [UploadedFile::fake()->create('virus.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors('foto_selesai.0');

        $this->assertSame('approved', $aduan->fresh()->status);
    }

    public function test_siswa_tidak_bisa_menyelesaikan_aduan(): void
    {
        ['siswa' => $siswa, 'kategori' => $k, 'lokasi' => $l] = $this->siapkan();

        $aduan = $siswa->aduan()->create([
            'kategori_kerusakan_id' => $k->id, 'lokasi_id' => $l->id,
            'tanggal_kejadian' => now(), 'deskripsi' => 'Kran bocor di toilet.', 'status' => 'approved',
        ]);

        $this->actingAs($siswa)->post(route('admin.aduan.selesai', $aduan))->assertForbidden();
    }

    public function test_login_dibatasi_setelah_banyak_percobaan(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['identitas' => 'x', 'password' => 'salah', 'login_as' => 'admin']);
        }

        $this->post('/login', ['identitas' => 'x', 'password' => 'salah', 'login_as' => 'admin'])
            ->assertStatus(429);
    }
}