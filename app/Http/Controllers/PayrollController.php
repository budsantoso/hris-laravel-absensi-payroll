<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use App\Services\PayrollService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isOwner()) {
            // Owner lihat semua payroll
            $payrolls = \App\Models\Payroll::with(['employee.user'])->latest()->paginate(10);
            return view('owner.payroll-report', compact('payrolls'));
        }

        if ($user->isHrd()) {
            // HRD lihat payroll karyawannya
            $payrolls = \App\Models\Payroll::whereHas('employee', function ($q) {
                $q->where('user_id', auth()->id());
            })->latest()->paginate(10);
            return view('hrd.payroll-report', compact('payrolls'));
        }

        if ($user->isKaryawan()) {
            // Karyawan lihat payroll miliknya
            $employee = $user->employee;
            $payrolls = \App\Models\Payroll::where('employee_id', $employee->id)->latest()->paginate(10);
            return view('karyawan.payroll-history', compact('payrolls'));
        }

        return redirect()->route('karyawan.dashboard');
    }

    public function generatePayroll($year, $month, Employee $employee)
    {
        $service = new PayrollService();
        $result = $service->calculateMonthlyPayroll($employee, $year, $month);

        // Cek apakah payroll sudah ada
        $existing = \App\Models\Payroll::where('employee_id', $employee->id)
            ->whereYear('bulan', $year)
            ->whereMonth('bulan', $month)
            ->first();

        if ($existing) {
            return back()->with('error', 'Payroll bulan ini sudah dibuat sebelumnya.');
        }

        // Buat payroll baru
        $payroll = \App\Models\Payroll::create([
            'employee_id' => $employee->id,
            'bulan' => $month,
            'tahun' => $year,
            'gaji_pokok' => $result['gaji_pokok'],
            'tunjangan' => $result['tunjangan'],
            'bonus' => 0,
            'total_terlambat' => $result['total_terlambat'],
            'total_alpha' => $result['total_alpha'],
            'total_potongan' => $result['total_potongan'],
            'total_gaji_bersih' => $result['total_gaji_bersih'],
            'status' => 'draft',
        ]);

        return back()->with('success', 'Payroll untuk bulan-' . $month . '/' . $year . ' berhasil dibuat. Status: Draft.');
    }

    public function publishPayroll(Request $request, $id)
    {
        $payroll = \App\Models\Payroll::findOrFail($id);

        // Hanya Owner yang bisa publish
        if (!auth()->user()->isOwner()) {
            return redirect()->route('karyawan.dashboard')->with('error', 'Akses ditolak.');
        }

        $payroll->update([
            'status' => 'approved',
        ]);

        return back()->with('success', 'Payroll bulan-' . $payroll->bulan . '/' . $payroll->tahun . ' berhasil dipublikasikan.');
    }

    public function downloadSlip($id)
    {
        $payroll = \App\Models\Payroll::with(['employee.user'])->findOrFail($id);
        $pdf = PDF::loadView('pdf.slip-gaji', compact('payroll'));
        
        return $pdf->download('slip-gaji-' . $payroll->employee->user->name . '-' . sprintf('%02d', $payroll->bulan) . '-' . $payroll->tahun . '.pdf');
    }
}