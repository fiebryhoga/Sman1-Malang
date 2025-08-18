<!DOCTYPE html>
<html>
<body>
    <table>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 16px; font-weight: bold;">JURNAL MENGAJAR GURU</th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 14px; font-weight: bold;">SMKN 1 SUKOREJO</th>
        </tr>
        <tr><th colspan="8"></th></tr>
        <tr>
            <td style="font-weight: bold;">Kelas</td>
            <td colspan="3">: {{ $namaKelas }}</td>
            <td style="font-weight: bold;">Mapel</td>
            <td colspan="3">: {{ $namaMapel }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Periode</td>
            <td colspan="7">: {{ $bulan }}</td>
        </tr>
        <thead>
            <tr>
                <th>No</th>
                <th>Hari, Tanggal</th>
                <th>Materi Pembahasan</th>
                <th>Detail Pembelajaran</th>
                <th>Hadir</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Alpha</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jurnals as $index => $jurnal)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center;">{{ $jurnal->tanggal->translatedFormat('l, d F Y') }}</td>
                <td>{{ $jurnal->materi }}</td>
                <td>{{ $jurnal->detail_pembelajaran }}</td>
                <td style="text-align: center;">{{ $jurnal->hadir_count }}</td>
                <td style="text-align: center;">{{ $jurnal->sakit_count }}</td>
                <td style="text-align: center;">{{ $jurnal->izin_count }}</td>
                <td style="text-align: center;">{{ $jurnal->alpha_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <tr>
            <td colspan="5" style="border: none;"></td>
            <td colspan="3" style="border: none; text-align: center; padding-top: 20px;">
                Pasuruan, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
                Guru Mata Pelajaran,
                <br><br><br><br><br><br>
                <b><u>{{ $namaGuru }}</u></b>
            </td>
        </tr>
    </table>
</body>
</html>