<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string
        $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string
        $title = 'Profil Saya';
    protected static bool $shouldRegisterNavigation = false;
    protected static string $view = 'filament.pages.edit-profile';

    // Properti untuk menampung data dari masing-masing form
    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        // Isi form profil dengan data user yang sedang login
        $this->profileForm->fill(auth()->user()->attributesToArray());
        // Kosongkan form password
        $this->passwordForm->fill();
    }
    
    /**
     * Mendaftarkan kedua form kita ke halaman ini.
     */
    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }
    
    // Definisi untuk form profil
    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Profil')
                    ->description('Perbarui informasi profil dan foto Anda.')
                    ->schema([
                        FileUpload::make('avatar_url')->label('Foto Profil')->image()->avatar()->imageEditor()->circleCropper()->disk('public')->directory('avatars'),
                        TextInput::make('name')->label('Nama Lengkap')->required(),
                        TextInput::make('nip')->label('NIP')->disabled(),
                        TextInput::make('email')->label('Email (Opsional)')->email(),
                        TextInput::make('no_telepon')->label('No. Telepon (Opsional)')->tel(),
                    ])->columns(2),
            ])
            ->model(auth()->user())
            ->statePath('profileData');
    }

    // Definisi untuk form password
    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                 Section::make('Ubah Password')
                    ->description('Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.')
                    ->schema([
                        TextInput::make('current_password')->label('Password Saat Ini')->password()->required()->currentPassword(),
                        TextInput::make('new_password')->label('Password Baru')->password()->required()->rule(Password::default())->different('current_password')->confirmed(),
                        TextInput::make('new_password_confirmation')->label('Konfirmasi Password Baru')->password()->required(),
                    ])->columns(2),
            ])
            ->statePath('passwordData');
    }
    
    // Aksi untuk tombol simpan di form profil
    protected function getProfileFormActions(): array
    {
        return [
            Action::make('saveProfile')->label('Simpan Perubahan')->submit('saveProfile'),
        ];
    }
    
    // Aksi untuk tombol simpan di form password
    protected function getPasswordFormActions(): array
    {
        return [
            Action::make('savePassword')->label('Ubah Password')->submit('savePassword'),
        ];
    }

    // Logika saat form profil disimpan
    public function saveProfile(): void
    {
        $user = auth()->user();
        $data = $this->profileForm->getState();
        $user->update($data);
        Notification::make()->title('Profil berhasil diperbarui')->success()->send();
        $this->dispatch('refresh-page');
    }

    // Logika saat form password disimpan
    public function savePassword(): void
    {
        $user = auth()->user();
        $data = $this->passwordForm->getState();
        $user->update(['password' => Hash::make($data['new_password'])]);
        $this->passwordForm->fill();
        Notification::make()->title('Password berhasil diubah')->success()->send();
    }
}