<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedPermissions();
        $this->seedSettings();
        $this->seedAdminUser();
        $this->assignPermissionsToRoles();
    }

    private function seedRoles(): void
    {
        $roles = [
            ['slug' => 'admin', 'name' => 'Administrator', 'description' => 'Akses penuh ke seluruh sistem', 'is_system' => true],
            ['slug' => 'librarian', 'name' => 'Pustakawan', 'description' => 'Mengelola koleksi dan sirkulasi', 'is_system' => true],
            ['slug' => 'teacher', 'name' => 'Guru', 'description' => 'Anggota perpustakaan (guru)', 'is_system' => false],
            ['slug' => 'student', 'name' => 'Siswa', 'description' => 'Anggota perpustakaan (siswa)', 'is_system' => false],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }

    private function seedPermissions(): void
    {
        $permissions = [
            // Book management
            ['slug' => 'book.view', 'name' => 'Lihat Buku', 'module' => 'library', 'description' => 'Melihat daftar dan detail buku'],
            ['slug' => 'book.create', 'name' => 'Tambah Buku', 'module' => 'library', 'description' => 'Menambah buku baru'],
            ['slug' => 'book.update', 'name' => 'Ubah Buku', 'module' => 'library', 'description' => 'Mengubah data buku'],
            ['slug' => 'book.delete', 'name' => 'Hapus Buku', 'module' => 'library', 'description' => 'Menghapus buku'],

            // Book item management
            ['slug' => 'book-item.view', 'name' => 'Lihat Eksemplar', 'module' => 'library', 'description' => 'Melihat eksemplar buku'],
            ['slug' => 'book-item.create', 'name' => 'Tambah Eksemplar', 'module' => 'library', 'description' => 'Menambah eksemplar buku'],
            ['slug' => 'book-item.update', 'name' => 'Ubah Eksemplar', 'module' => 'library', 'description' => 'Mengubah data eksemplar'],
            ['slug' => 'book-item.delete', 'name' => 'Hapus Eksemplar', 'module' => 'library', 'description' => 'Menghapus eksemplar'],

            // Member management
            ['slug' => 'member.view', 'name' => 'Lihat Anggota', 'module' => 'library', 'description' => 'Melihat daftar anggota'],
            ['slug' => 'member.create', 'name' => 'Tambah Anggota', 'module' => 'library', 'description' => 'Menambah anggota baru'],
            ['slug' => 'member.update', 'name' => 'Ubah Anggota', 'module' => 'library', 'description' => 'Mengubah data anggota'],
            ['slug' => 'member.delete', 'name' => 'Hapus Anggota', 'module' => 'library', 'description' => 'Menghapus anggota'],

            // Borrow / circulation
            ['slug' => 'borrow.view', 'name' => 'Lihat Peminjaman', 'module' => 'library', 'description' => 'Melihat data peminjaman'],
            ['slug' => 'borrow.create', 'name' => 'Proses Peminjaman', 'module' => 'library', 'description' => 'Memproses peminjaman buku'],
            ['slug' => 'borrow.return', 'name' => 'Proses Pengembalian', 'module' => 'library', 'description' => 'Memproses pengembalian buku'],
            ['slug' => 'borrow.extend', 'name' => 'Perpanjang Peminjaman', 'module' => 'library', 'description' => 'Memperpanjang masa pinjam'],

            // Reservation
            ['slug' => 'reservation.view', 'name' => 'Lihat Reservasi', 'module' => 'library', 'description' => 'Melihat data reservasi'],
            ['slug' => 'reservation.create', 'name' => 'Buat Reservasi', 'module' => 'library', 'description' => 'Membuat reservasi buku'],
            ['slug' => 'reservation.cancel', 'name' => 'Batalkan Reservasi', 'module' => 'library', 'description' => 'Membatalkan reservasi'],

            // Fine management
            ['slug' => 'fine.view', 'name' => 'Lihat Denda', 'module' => 'library', 'description' => 'Melihat data denda'],
            ['slug' => 'fine.pay', 'name' => 'Bayar Denda', 'module' => 'library', 'description' => 'Memproses pembayaran denda'],
            ['slug' => 'fine.waive', 'name' => 'Hapus Denda', 'module' => 'library', 'description' => 'Menghapuskan denda'],

            // System
            ['slug' => 'setting.view', 'name' => 'Lihat Pengaturan', 'module' => 'system', 'description' => 'Melihat pengaturan sistem'],
            ['slug' => 'setting.update', 'name' => 'Ubah Pengaturan', 'module' => 'system', 'description' => 'Mengubah pengaturan sistem'],
            ['slug' => 'user.view', 'name' => 'Lihat Pengguna', 'module' => 'system', 'description' => 'Melihat daftar pengguna'],
            ['slug' => 'user.create', 'name' => 'Tambah Pengguna', 'module' => 'system', 'description' => 'Menambah pengguna baru'],
            ['slug' => 'user.update', 'name' => 'Ubah Pengguna', 'module' => 'system', 'description' => 'Mengubah data pengguna'],
            ['slug' => 'user.delete', 'name' => 'Hapus Pengguna', 'module' => 'system', 'description' => 'Menghapus pengguna'],
            ['slug' => 'role.view', 'name' => 'Lihat Peran', 'module' => 'system', 'description' => 'Melihat daftar peran'],
            ['slug' => 'role.manage', 'name' => 'Kelola Peran', 'module' => 'system', 'description' => 'Mengelola peran dan hak akses'],
            ['slug' => 'module.view', 'name' => 'Lihat Modul', 'module' => 'system', 'description' => 'Melihat daftar modul'],
            ['slug' => 'module.manage', 'name' => 'Kelola Modul', 'module' => 'system', 'description' => 'Mengaktifkan/menonaktifkan modul'],
            ['slug' => 'theme.view', 'name' => 'Lihat Tema', 'module' => 'system', 'description' => 'Melihat daftar tema'],
            ['slug' => 'theme.manage', 'name' => 'Kelola Tema', 'module' => 'system', 'description' => 'Mengubah tema aktif'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['group' => 'app', 'key' => 'app_name', 'value' => 'CosmicLib', 'type' => 'string', 'is_public' => true],
            ['group' => 'app', 'key' => 'app_description', 'value' => 'Sistem Manajemen Perpustakaan', 'type' => 'string', 'is_public' => true],
            ['group' => 'app', 'key' => 'app_locale', 'value' => 'id', 'type' => 'string', 'is_public' => true],
            ['group' => 'library', 'key' => 'loan_period_days', 'value' => '7', 'type' => 'integer', 'is_public' => false],
            ['group' => 'library', 'key' => 'max_extend_count', 'value' => '2', 'type' => 'integer', 'is_public' => false],
            ['group' => 'library', 'key' => 'extend_days', 'value' => '7', 'type' => 'integer', 'is_public' => false],
            ['group' => 'library', 'key' => 'overdue_fine_per_day', 'value' => '1000', 'type' => 'integer', 'is_public' => false],
            ['group' => 'library', 'key' => 'reservation_hold_hours', 'value' => '48', 'type' => 'integer', 'is_public' => false],
            ['group' => 'library', 'key' => 'max_borrow_per_member', 'value' => '5', 'type' => 'integer', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }

    private function seedAdminUser(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@cosmiclib.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@cosmiclib.test',
                'password' => Hash::make('password'),
            ]
        );

        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole && ! $admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }
    }

    private function assignPermissionsToRoles(): void
    {
        $admin = Role::where('slug', 'admin')->first();
        if ($admin) {
            $admin->permissions()->sync(Permission::pluck('id'));
        }

        $librarian = Role::where('slug', 'librarian')->first();
        if ($librarian) {
            $librarianPerms = Permission::where('module', 'library')->pluck('id');
            $librarian->permissions()->sync($librarianPerms);
        }

        $memberPerms = Permission::whereIn('slug', [
            'book.view',
            'borrow.view',
            'reservation.view',
            'reservation.create',
            'reservation.cancel',
            'fine.view',
        ])->pluck('id');

        foreach (['teacher', 'student'] as $slug) {
            $role = Role::where('slug', $slug)->first();
            if ($role) {
                $role->permissions()->sync($memberPerms);
            }
        }
    }
}
