<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\CompanySetting;
use Carbon\Carbon;

class PayrollService
{
    public function calculateMonthlyPayroll(Employee $employee, $year, $month)
    {
        // Get attendance data for the month
        $startDate = Carbon::create($year, $month, 1);
        $endDate = Carbon::create($year, $month, $endDate)->lastOfMonth();
        
        $attendances = Attendance::where('user_id', $employee->id)
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        // Calculate total hours worked
        $totalHours = 0;
        $totalTerlambat = 0;
        $totalAlpha = 0;
        $totalCuti = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->status === 'hadir' || $attendance->status === 'terlambat') {
                // Count full working hours (8 hours = 1 hari kerja)
                $hours = $this->calculateWorkingHours($attendance->jam_masuk, $attendance->jam_pulang);
                $totalHours += $hours;
            }

            // Hitung potongan
            if ($attendance->status === 'terlambat') {
                $totalTerlambat++;
            } elseif ($attendance->status === 'alpha') {
                $totalAlpha++;
            } elseif ($attendance->status === 'cuti') {
                $totalCuti++;
            }
        }

        // Get company settings
        $settings = CompanySetting::first();
        $jamMasukKantor = $settings ? Carbon::parse($settings->jam_masuk_kantor)->format('H:i:s') : '08:00:00';
        $jamPulangKantor = $settings ? Carbon::parse($settings->jam_pulang_kantor)->format('H:i:s') : '17:00:00';
        $dendaTerlambatPerJam = $settings ? 25000 : 25000; // Default: Rp25,000 per hour
        $dendaAlphaPerhari = $settings ? 100000 : 100000; // Default: Rp100,000 per day

        // Calculate base gaji
        $gajiPokok = $employee->gaji_pokok;
        $tunjangan = $employee->tunjangan;

        // Calculate total potongan
        $totalPotongan = ($totalTerlambat * $dendaTerlambatPerjam) + ($totalAlpha * $dendaAlphaPerhari);

        // Calculate total gaji bersih
        $totalGajiBersih = $gajiPokok + $tunjangan - $totalPotongan;

        return [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'potongan_terlambat' => $totalTerlambat * $dendaTerlambatPerjam,
            'potongan_alpha' => $totalAlpha * $dendaAlphaPerhari,
            'total_potongan' => $totalPotongan,
            'total_gaji_bersih' => $totalGajiBersih,
            'total_hours' => $totalHours,
            'status' => 'draft', // default status, will be updated by Owner
        ];
    }

    private function calculateWorkingHours($jamMasuk, $jamPulang)
    {
        $start = Carbon::parse($jamMasuk);
        $end = Carbon::parse($jamPulang);
        
        // Calculate total hours
        $hours = $start->diffInHours($end);
        
        // If working hours exceed 8 hours (full day), count as 8
        return min($hours, 8);
    }
}