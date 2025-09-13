<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile; // ✅ 1. Impor halaman baru
use App\Filament\Pages\Login;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\MenuItem; // ✅ 2. Impor MenuItem
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->brandName('SMAN 1 Malang')
            ->colors(['primary' => Color::Pink])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class, // ✅ 3. Daftarkan halaman profil di sini
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            // ✅ 4. Tambahkan link ke menu pengguna di pojok kanan atas
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profil Saya')
                    ->url(fn (): string => EditProfile::getUrl())
                    ->icon('heroicon-o-user-circle'),
                // Anda bisa menambahkan item menu lain di sini jika perlu
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}