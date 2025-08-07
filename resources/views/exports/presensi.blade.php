<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Kehadiran Siswa</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header-section { margin-bottom: 20px; }
        .school-info { text-align: center; }
        .school-info h2 { font-size: 18px; font-weight: bold; }
        .school-info p { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        .table-header { background-color: #f2f2f2; font-weight: bold; }
        .cell-hadir { background-color: #d4edda; }
        .cell-sakit { background-color: #fff3cd; }
        .cell-izin { background-color: #d1ecf1; }
        .cell-alpha { background-color: #f8d7da; }
        .bold { font-weight: bold; }
        .logo-cell { vertical-align: middle; }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th colspan="3" class="school-info" style="border: none;"></th>
                <th colspan="{{ count($dates) + 4 }}" class="school-info" style="border: none;">
                    <b style="font-size: 18px;">DAFTAR KEHADIRAN SISWA</b>
                    <br>
                    <b style="font-size: 18px;">SMP ISLAM BAITUSSALAM</b>
                    <br>
                    <span style="font-size: 12px;">Jalan Kebenaran No. 07 Kampung Merdeka, Kabupaten Suryadelima, Jawa Timur</span>
                </th>
            </tr>
            <tr style="height: 10px;">
                <th colspan="{{ count($dates) + 7 }}" style="border: none;"></th>
            </tr>
            <tr>
                <th colspan="3" style="text-align: left; border: none; padding-top: 10px;">
                    <b>Kelas: &nbsp;</b> {{ $kelas }}<br>
                    <b>Mata Pelajaran: &nbsp;</b> {{ $mataPelajaran }}<br>
                    <b>Bulan: &nbsp;</b> {{ $monthName }}
                </th>
                <th colspan="{{ count($dates) + 4 }}" style="border: none;"></th>
            </tr>
            <tr class="table-header">
                <th rowspan="2" style="width: 30px;"><b>No</b></th>
                <th rowspan="2" style="width: 150px;"><b>Nama</b></th>
                <th rowspan="2" style="width: 30px;"><b>L/P</b></th>
                <th colspan="{{ count($dates) }}"><b>Tanggal</b></th>
                <th colspan="4"><b>Keterangan</b></th>
            </tr>
            <tr class="table-header">
                @foreach($dates as $date)
                    <th style="width: 25px;"><b>{{ \Carbon\Carbon::parse($date)->day }}</b></th>
                @endforeach
                <th style="width: 25px;"><b>H</b></th>
                <th style="width: 25px;"><b>S</b></th>
                <th style="width: 25px;"><b>I</b></th>
                <th style="width: 25px;"><b>A</b></th>
            </tr>
        </thead>
        <tbody>
            @foreach($siswas as $index => $siswa)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->jenis_kelamin }}</td>
                    @foreach($dates as $date)
                        @php
                            $status = '';
                            if (isset($presensis[\Carbon\Carbon::parse($date)->format('d')])) {
                                $presensiData = $presensis[\Carbon\Carbon::parse($date)->format('d')];
                                $detail = $presensiData['detail']->get($siswa->id);
                                if ($detail) {
                                    $status = $detail->status;
                                }
                            }
                        @endphp
                        <td class="cell-{{ $status ?? 'alpha' }}">
                            {{ $status === 'hadir' ? 'H' : strtoupper(substr($status, 0, 1)) }}
                        </td>
                    @endforeach
                    <td>{{ $rekapPerSiswa[$siswa->id]['hadir'] ?? 0 }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['sakit'] ?? 0 }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['izin'] ?? 0 }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['alpha'] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>