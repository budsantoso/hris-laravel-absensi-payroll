<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\CompanySetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HRISSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat pengguna (users) dengan role berbeda
        $owner = User::create([
            'name' => 'Admin Utama',
            'email' => 'owner@perusahaan.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $hrd = User::create([
            'name' => 'Kepala HR',
            'email' => 'hrd@perusahaan.com',
            'password' => Hash::make('password'),
            'role' => 'hrd',
        ]);

        // 3 karyawan
        $karyawan1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'karyawan1@perusahaan.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);

        $karyawan2 = User::create([
            'name' => 'Siti Aminah',
            'email' => 'karyawan2@perusahaan.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);

        $karyawan3 = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'karyawan3@perusahaan.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
        ]);

        // 2. Buat data karyawan (employees) yang relasi ke users
        Employee::create([
            'user_id' => $owner->id,
            'nip' => 'OWN-001',
            'jabatan' => 'Owner',
            'departemen' => 'Direksi',
            'tanggal_masuk' => '2020-01-01',
            'gaji_pokok' => 20000000.00,
            'tunjangan_tetap' => 5000000.00,
            'nama_bank' => 'Bank XYZ',
            'no_rekening' => '1234567890',
            'kuota_cuti' => 12,
            'status_aktif' => true,
        ]);

        Employee::create([
            'user_id' => $hrd->id,
            'nip' => 'HRD-001',
            'jabatan' => 'Kepala HR',
            'departemen' => 'Human Resources',
            'tanggal_masuk' => '2020-03-15',
            'gaji_pokok' => 8000000.00,
            'tunjangan_tetap' => 2000000.00,
            'nama_bank' => 'Bank XYZ',
            'no_rekening' => '0987654321',
            'kuota_cuti' => 12,
            'status_aktif' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan1->id,
            'nip' => 'KW-001',
            'jabatan' => 'Manager',
            'departemen' => 'Produksi',
            'tanggal_masuk' => '2021-06-01',
            'gaji_pokok' => 5500000.00,
            'tunjangan_tetap' => 1500000.00,
            'nama_bank' => 'Bank XYZ',
            'no_rekening' => '1112223334',
            'kuota_cuti' => 12,
            'status_aktif' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan2->id,
            'nip' => 'KW-002',
            'jabatan' => 'Supervisor',
            'departemen' => 'Pemasaran',
            'tanggal_masuk' => '2021-09-15',
            'gaji_pokok' => 4800000.00,
            'tunjangan_tetap' => 1200000.00,
            'nama_bank' => 'Bank XYZ',
            'no_rekening' => '4445556667',
            'kuota_cuti' => 12,
            'status_aktif' => true,
        ]);

        Employee::create([
            'user_id' => $karyawan3->id,
            'nip' => 'KW-003',
            'jabatan' => 'Operator',
            'departemen' => 'Produksi',
            'tanggal_masuk' => '2022-02-10',
            'gaji_pokok' => 3800000.00,
            'tunjangan_tetap' => 1000000.00,
            'nama_bank' => 'Bank XYZ',
            'no_rekening' => '7778889990',
            'kuota_cuti' => 12,
            'status_aktif' => true,
        ]);

        // 3. Insert company_settings default
        CompanySetting::create([
            'nama_perusahaan' => 'PT Swadaya Sejahtera',
            'alamat_perusahaan' => 'Jl. Mawar No. 12, Kota Tangerang, Banten',
            'jam_masuk_kantor' => '08:00:00',
            'jam_pulang_kantor' => '17:00:00',
            'denda_terlambat' => 25000.00,
            'denda_alpha' => 100000.00,
        ]);
    }
}