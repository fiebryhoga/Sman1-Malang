<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <table>
        <tr>
            <th colspan="3" style="border: none;"></th>
            <th colspan="{{ $presensis->count() + 4 }}" style="border: none; text-align: center;">
                <b style="font-size: 16px;">DAFTAR KEHADIRAN SISWA</b><br>
                <b style="font-size: 14px;">SMKN 1 SUKOREJO</b><br>
                <span style="font-size: 10px;">Jl. Sumbergareng, Krajan, Sukorejo, Kec. Sukorejo, Pasuruan, Jawa Timur</span>
            </th>
        </tr>
        <tr>
            <th colspan="{{ $presensis->count() + 7 }}" style="border: none;"></th>
        </tr>
        <tr>
            <th colspan="3" style="text-align: left; border: none; font-weight: bold;">
                Kelas<br>
                Mata Pelajaran<br>
                Bulan
            </th>
            <th colspan="{{ $presensis->count() + 4 }}" style="text-align: left; border: none;">
                : {{ $namaKelas }}<br>
                : {{ $namaMapel }}<br>
                : {{ $bulan }}
            </th>
        </tr>
        <tr>
            <th colspan="{{ $presensis->count() + 7 }}" style="border: none;"></th>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;"><b>No</b></th>
                <th rowspan="2" style="width: 150px;"><b>Nama</b></th>
                <th rowspan="2" style="width: 30px;"><b>L/P</b></th>
                <th colspan="{{ $presensis->count() }}"><b>Tanggal</b></th>
                <th colspan="4"><b>Keterangan</b></th>
            </tr>
            <tr>
                @foreach($presensis as $presensi)
                    <th style="width: 25px;"><b>{{ $presensi->tanggal->format('d/m/Y') }}</b></th>
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
                    <td>{{ strtoupper(substr($siswa->jenis_kelamin, 0, 1)) }}</td>
                    @foreach($presensis as $presensi)
                        <td>
                            {{ $rekapData[$siswa->id][$presensi->tanggal->format('Y-m-d')] ?? 'A' }}
                        </td>
                    @endforeach
                    <td>{{ $rekapPerSiswa[$siswa->id]['H'] ?: '' }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['S'] ?: '' }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['I'] ?: '' }}</td>
                    <td>{{ $rekapPerSiswa[$siswa->id]['A'] ?: '' }}</td>
                </tr>
            @endforeach
            
            <tr>
                <td colspan="3" style="text-align: right; font-weight: bold;">Detail Pembelajaran</td>
                @foreach($presensis as $presensi)
                <td style="font-size: 8px; white-space: normal; text-align: left;">{{ $presensi->detail_pembelajaran }}</td>
                @endforeach
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td colspan="{{ $presensis->count() }}" style="border: none;"></td>
            <td colspan="7" style="border: none; text-align: center; padding-top: 20px;">
                Pasuruan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Guru Mata Pelajaran,
                <br><br><br><br><br><br>
                <b><u>{{ $namaGuru }}</u></b>
            </td>
        </tr>
    </table>
</body>
</html>