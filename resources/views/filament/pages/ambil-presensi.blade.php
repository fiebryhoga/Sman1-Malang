<x-filament-panels::page>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4">Detail Presensi</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p><strong>Kelas:</strong> {{ $this->presensi->kelas->nama ?? '-' }}</p>
                <p><strong>Mata Pelajaran:</strong> {{ $this->presensi->mataPelajaran->nama ?? '-' }}</p>
            </div>
            <div>
                <p><strong>Tanggal:</strong> {{ $this->presensi->tanggal->format('d M Y') ?? '-' }}</p>
                <p><strong>Pertemuan Ke-:</strong> {{ $this->presensi->pertemuan_ke ?? '-' }}</p>
                <p><strong>Materi:</strong> {{ $this->presensi->materi_pertemuan ?? '-' }}</p>
            </div>
        </div>
    </x-filament::card>

    {{ $this->form }}

    <div class="mt-6">
        <x-filament::button wire:click="submit">
            Simpan Presensi
        </x-filament::button>
    </div>
</x-filament-panels::page>