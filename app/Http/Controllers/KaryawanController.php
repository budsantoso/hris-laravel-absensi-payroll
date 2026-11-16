<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;

class KaryawanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:karyawan');
    }

    public function dashboard()
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Ambil absensi hari ini
        $today = Carbon::today();
        $attendance = $employee->attendances()->where('tanggal', $today)->first();

        // Data company settings
        $settings = \App\Models\CompanySetting::first();

        return view('karyawan.dashboard', compact('user', 'employee', 'attendance', 'settings'));
    }

    public function absensi(Request $request)
    {
        $user = auth()->user();
        $employee = $user->employee;

        // Validasi: hanya bisa clock-in sekali per hari
        $today = Carbon::today();
        $existing = $employee->attendances()->where('tanggal', $today)->first();

        if ($request->type === 'clock-in') {
            if ($existing) {
                return back()->with('error', 'Anda sudah melakukan clock-in hari ini.');
            }

            // Validasi jam masuk berdasarkan company_settings
            $settings = \App\Models\CompanySetting::first();
            $jamMasuk = $settings ? $settings->jam_masuk_kantor : '08:00:00';
            $now = Carbon::now()->format('H:i:s');

            $status = 'hadir';
            $terlambat = Carbon::parse($now)->gt(Carbon::parse($jamMasuk));

            if ($terlambat) {
                $status = 'terlambat';
            }

            $employee->attendances()->create([
                'tanggal' => $today->toDateString(),
                'jam_masuk' => $now,
                'status' => $status,
                'lokasi_masuk' => $request->lokasi ?? null,
                'keterangan' => $request->keterangan ?? null,
            ]);

            return back()->with('success', 'Clock-In berhasil! Status: ' . ucfirst($status));
        }

        if ($request->type === 'clock-out') {
            if (!$existing) {
                return back()->with('error', 'Anda belum melakukan clock-in hari ini.');
            }

            if ($existing->jam_pulang) {
                return back()->with('error', 'Anda sudah melakukan clock-out.');
            }

            $existing->jam_pulang = Carbon::now()->format('H:i:s');
            $existing->save();

            return back()->with('success', 'Clock-Out berhasil!');
        }

        return back()->with('error', 'Aksi tidak valid.');
    }
}