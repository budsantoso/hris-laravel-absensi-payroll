<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan - HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-800">Dashboard Karyawan</h1>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }} ({{ auth()->user()->employee->nip }})</span>
                        <a href="{{ route('logout') }}" class="text-sm text-red-600 hover:underline">Logout</a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Welcome Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Selamat Datang, {{ auth()->user()->name }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="p-4 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-700">Jabatan</p>
                                <p class="text-lg font-semibold text-blue-900">{{ auth()->user()->employee->jabatan }}</p>
                            </div>
                            <div class="p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-700">Departemen</p>
                                <p class="text-lg font-semibold text-green-900">{{ auth()->user()->employee->departemen }}</p>
                            </div>
                            <div class="p-4 bg-yellow-50 rounded-lg">
                                <p class="text-sm text-yellow-700">Sisa Cuti</p>
                                <p class="text-lg font-semibold text-yellow-900">{{ auth()->user()->employee->kuota_cuti }} hari</p>
                            </div>
                        </div>
                    </div>

                    <!-- Absensi Hari Ini -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Absensi Hari Ini ({{ \Carbon\Carbon::today()->locale('id')->isoFormat('dddd, DD MMMM YYYY') }})</h2>
                        
                        @if($attendance)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="p-4 {{ $attendance->status === 'terlambat' ? 'bg-red-50' : 'bg-green-50' }} rounded-lg">
                                    <p class="text-sm text-gray-600">Status</p>
                                    <p class="text-lg font-semibold {{ $attendance->status === 'terlambat' ? 'text-red-900' : 'text-green-900' }}">
                                        {{ ucfirst($attendance->status) }}
                                    </p>
                                </div>
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-700">Jam Masuk</p>
                                    <p class="text-lg font-semibold text-blue-900">
                                        {{ $attendance->jam_masuk ? \Carbon\Carbon::parse($attendance->jam_masuk)->format('H:i') : '-' }}
                                    </p>
                                </div>
                                <div class="p-4 bg-purple-50 rounded-lg">
                                    <p class="text-sm text-purple-700">Jam Pulang</p>
                                    <p class="text-lg font-semibold text-purple-900">
                                        {{ $attendance->jam_pulang ? \Carbon\Carbon::parse($attendance->jam_pulang)->format('H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                            
                            @if(!$attendance->jam_pulang)
                                <div class="mt-4">
                                    <form action="{{ route('karyawan.absensi') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="clock-out">
                                        <button type="submit" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                            Clock-Out
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="mt-4 text-center">
                                    <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg">Absensi hari ini sudah selesai</span>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-600 mb-4">Anda belum melakukan absensi hari ini.</p>
                                <form action="{{ route('karyawan.absensi') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="clock-in">
                                    <input type="hidden" name="lokasi" id="lokasi" value="">
                                    <button type="submit" onclick="getLocation()" class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition text-lg">
                                        Clock-In
                                    </button>
                                </form>
                                <p class="text-sm text-gray-500 mt-2">Lokasi akan diambil otomatis via GPS browser</p>
                            </div>
                        @endif
                    </div>

                    <!-- Quick Links -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('karyawan.slip-gaji') }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Slip Gaji</h3>
                            <p class="text-gray-600">Lihat dan unduh riwayat slip gaji bulanan</p>
                        </a>
                        <a href="#" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Pengajuan Cuti/Izin</h3>
                            <p class="text-gray-600">Ajukan cuti, izin, atau sakit secara online</p>
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('lokasi').value = position.coords.latitude + ',' + position.coords.longitude;
                    },
                    (error) => {
                        console.log('GPS not available:', error);
                    }
                );
            }
        }
    </script>
</body>
</html>