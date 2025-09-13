<x-filament-panels::page>
    {{-- Form untuk memperbarui data profil --}}
    <form wire:submit="saveProfile">
        {{ $this->profileForm }}

        <x-filament-panels::form.actions 
            :actions="$this->getProfileFormActions()" 
        />
    </form>

    <div class="border-t border-gray-200 dark:border-white/10 my-6"></div>

    {{-- Form untuk memperbarui password --}}
    <form wire:submit="savePassword">
        {{ $this->passwordForm }}

        <x-filament-panels::form.actions 
            :actions="$this->getPasswordFormActions()" 
        />
    </form>
</x-filament-panels::page>