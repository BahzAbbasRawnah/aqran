<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('أقران | لوحة الإدارة')

            // Premium emerald-green color palette for Saudi academic context
            ->colors([
                'primary'  => Color::Emerald,
                'gray'     => Color::Slate,
                'info'     => Color::Sky,
                'success'  => Color::Teal,
                'warning'  => Color::Amber,
                'danger'   => Color::Rose,
            ])

            // RTL Arabic support with Tajawal font
            ->font('Tajawal', 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap')

            // Inject RTL direction into the HTML root via render hook (Filament v3 approach)
            ->renderHook(
                'panels::body.start',
                fn (): string => '<script>
                    document.documentElement.setAttribute("dir","rtl");
                    document.documentElement.setAttribute("lang","ar");
                </script>'
            )

            // Content max width
            ->maxContentWidth(MaxWidth::Full)

            // Auto-discover all resources from the Filament/Resources namespace
            ->discoverResources(
                in: app_path('Filament/Resources'),
                for: 'App\\Filament\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Pages'),
                for: 'App\\Filament\\Pages'
            )
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Widgets'),
                for: 'App\\Filament\\Widgets'
            )
            ->widgets([
                Widgets\AccountWidget::class,
            ])

            // Navigation groups in Arabic
            ->navigationGroups([
                'الإدارة الأكاديمية',
                'إدارة المستخدمين',
                'إدارة المحتوى',
                'الإشراف والمراجعة',
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
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
