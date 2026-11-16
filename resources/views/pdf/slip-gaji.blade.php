@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center">Slip Gaji Bulan {{ $payroll->bulan }} {{ $payroll->tahun }}</h1>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Karyawan</h2>
            <div class="text-sm text-gray-600">
                {{ $payroll->employee->user->name }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-gray-700">Nama</p>
                <p class="font-bold">{{ $payroll->employee->user->name }}</p>
            </div>
            <div>
                <p class="text-gray-700">NIP</p>
                <p class="font-bold">{{ $payroll->employee->nip }}</p>
            </div>
            <div>
                <p class="text-gray-700">Jabatan</p>
                <p class="font-bold">{{ $payroll->employee->jabatan }}</p>
            </div>
            <div>
                <p class="text-gray-700">Departemen</p>
                <p class="font-bold">{{ $payroll->employee->departemen }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-md mb-4">
            <h3 class="text-lg font-semibold mb-2">Rincian Gaji</h3>
            <table class="w-full">
                <tr>
                    <td>Gaji Pokok</td>
                    <td>{{ number_format($payroll->gaji_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Tunjangan Tetap</td>
                    <td>{{ number_format($payroll->tunjangan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Bonus Lembur</td>
                    <td>{{ number_format($payroll->bonus, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Pendapatan</td>
                    <td>{{ number_format($payroll->total_pendapatan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Potongan Terlambat</td>
                    <td>-{{ number_format($payroll->potongan_terlambat, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Potongan Alpha</td>
                    <td>-{{ number_format($payroll->potongan_alpha, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Potongan</td>
                    <td>-{{ number_format($payroll->total_potongan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Gaji Bersih</strong></td>
                    <td><strong>{{ number_format($payroll->total_gaji_bersih, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="mt-6 text-right">
            <p class="text-gray-600">Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p class="text-gray-600">Nama HRD: HRD Sistem</p>
        </div>
    </div>
</div>
@endsection