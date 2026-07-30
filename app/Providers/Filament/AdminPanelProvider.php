<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\Hashidable;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use CraftForge\FilamentLanguageSwitcher\FilamentLanguageSwitcherPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(Login::class)
            ->breadcrumbs(false)
            ->maxContentWidth('full')
            ->favicon(url('backend/images/JL.png'))
            ->colors([
                'danger' => [
                    50 => 'oklch(97.1% 0.013 17.38)',
                    100 => 'oklch(93.6% 0.032 17.717)',
                    200 => 'oklch(88.5% 0.062 18.334)',
                    300 => 'oklch(80.8% 0.114 19.571)',
                    400 => 'oklch(70.4% 0.191 22.216)',
                    500 => 'oklch(63.7% 0.237 25.331)',
                    600 => 'oklch(57.7% 0.245 27.325)',
                    700 => 'oklch(50.5% 0.213 27.518)',
                    800 => 'oklch(44.4% 0.177 26.899)',
                    900 => 'oklch(39.6% 0.141 25.723)',
                    950 => 'oklch(25.8% 0.092 26.042)',
                ],
                'gray' => [
                    50 => 'oklch(98.5% 0 0)',
                    100 => 'oklch(96.7% 0.001 286.375)',
                    200 => 'oklch(92% 0.004 286.32)',
                    300 => 'oklch(87.1% 0.006 286.286)',
                    400 => 'oklch(70.5% 0.015 286.067)',
                    500 => 'oklch(55.2% 0.016 285.938)',
                    600 => 'oklch(54.6% 0.245 262.881)',
                    700 => 'oklch(44.2% 0.017 285.786)',
                    800 => 'oklch(27.4% 0.006 286.033)',
                    900 => 'oklch(21% 0.006 285.885)',
                    950 => 'oklch(14.1% 0.005 285.823)',
                ],
                'info' => [
                    50 => 'oklch(97% 0.014 254.604)',
                    100 => 'oklch(93.2% 0.032 255.585)',
                    200 => 'oklch(88.2% 0.059 254.128)',
                    300 => 'oklch(80.9% 0.105 251.813)',
                    400 => 'oklch(70.7% 0.165 254.624)',
                    500 => 'oklch(62.3% 0.214 259.815)',
                    600 => 'oklch(54.6% 0.245 262.881)',
                    700 => 'oklch(48.8% 0.243 264.376)',
                    800 => 'oklch(42.4% 0.199 265.638)',
                    900 => 'oklch(37.9% 0.146 265.522)',
                    950 => 'oklch(28.2% 0.091 267.935)',
                ],
                'primary' => [
                    50 => 'oklch(97.7% 0.013 236.62)',
                    100 => 'oklch(95.1% 0.026 236.824)',
                    200 => 'oklch(90.1% 0.058 230.902)',
                    300 => 'oklch(82.8% 0.111 230.318)',
                    400 => 'oklch(74.6% 0.16 232.661)',
                    500 => 'oklch(68.5% 0.169 237.323)',
                    600 => 'oklch(58.8% 0.158 241.966)',
                    700 => 'oklch(50% 0.134 242.749)',
                    800 => 'oklch(44.3% 0.11 240.79)',
                    900 => 'oklch(39.1% 0.09 240.876)',
                    950 => 'oklch(29.3% 0.066 243.157)',
                ],
                'success' => [
                    50 => 'oklch(98.2% 0.018 155.826)',
                    100 => 'oklch(96.2% 0.044 156.743)',
                    200 => 'oklch(92.5% 0.084 155.995)',
                    300 => 'oklch(87.1% 0.15 154.449)',
                    400 => 'oklch(79.2% 0.209 151.711)',
                    500 => 'oklch(72.3% 0.219 149.579)',
                    600 => 'oklch(62.7% 0.194 149.214)',
                    700 => 'oklch(52.7% 0.154 150.069)',
                    800 => 'oklch(44.8% 0.119 151.328)',
                    900 => 'oklch(39.3% 0.095 152.535)',
                    950 => 'oklch(26.6% 0.065 152.934)',
                ],
                'warning' => [
                    50 => 'oklch(98.7% 0.022 95.277)',
                    100 => 'oklch(96.2% 0.059 95.617)',
                    200 => 'oklch(92.4% 0.12 95.746)',
                    300 => 'oklch(87.9% 0.169 91.605)',
                    400 => 'oklch(82.8% 0.189 84.429)',
                    500 => 'oklch(76.9% 0.188 70.08)',
                    600 => 'oklch(66.6% 0.179 58.318)',
                    700 => 'oklch(55.5% 0.163 48.998)',
                    800 => 'oklch(47.3% 0.137 46.201)',
                    900 => 'oklch(41.4% 0.112 45.904)',
                    950 => 'oklch(27.9% 0.077 45.635)',
                ],
            ])
            ->sidebarWidth('20rem')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                ->navigationGroup('Settings')   // put it in your existing group
                ->navigationSort(99)            // position within that group
                ->navigationIcon('heroicon-o-shield-check') // optional
                ->navigationLabel(__('global.roles')),
                FilamentLanguageSwitcherPlugin::make()
                ->locales(['km', 'en'])
                ->showOnAuthPages(),
            ])
            ->authMiddleware([
                Authenticate::class,
                Hashidable::class,
            ])
            ->spa()
            ->sidebarCollapsibleOnDesktop(true)
            ->globalSearch(false)
            ->darkMode(false)
            ->brandName('JinLong') // removes text app name
            ->brandLogo(asset("backend/images/JL.png"))
            // ->renderHook(
            //     PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE,
            //     fn (): string => view('filament.pages.auth.login-header')->render(),
            // )
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')'),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('
                    <!-- Custom Toast -->
                    <div id="back-blocked-toast" style="
                        display: none;
                        position: fixed;
                        top: 1.5rem;
                        right: 1.5rem;
                        z-index: 99999;
                        background: #f59e0b;
                        color: #fff;
                        padding: 0.85rem 1.25rem;
                        border-radius: 0.5rem;
                        font-size: 0.9rem;
                        font-weight: 500;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
                        display: flex;
                        align-items: center;
                        gap: 0.6rem;
                        opacity: 0;
                        transition: opacity 0.3s ease;
                        pointer-events: none;
                    ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        សូមចុចលើប្រព័ន្ធ ដើម្បីធ្វើការត្រឡប់ក្រោយ ឬមុខងារណាមួយ។
                    </div>

                    <script>
                        (function () {
                            const BUFFER = 10;
                            // let alertShown = false;
                            let toastTimer = null;
                            const toast = document.getElementById("back-blocked-toast");

                            function trapHistory() {
                                for (let i = 0; i < BUFFER; i++) {
                                    window.history.pushState({ trapped: true, i }, document.title, window.location.href);
                                }
                            }

                            function showNotification() {
                                // if (alertShown) return;
                                // alertShown = true;

                                // Show toast
                                toast.style.display = "flex";
                                requestAnimationFrame(() => { toast.style.opacity = "1"; });

                                // Hide after 3s
                                clearTimeout(toastTimer);
                                toastTimer = setTimeout(() => {
                                    toast.style.opacity = "0";
                                    setTimeout(() => { toast.style.display = "none"; }, 800);
                                    // alertShown = false;
                                }, 3000);
                            }

                            window.addEventListener("popstate", function () {
                                trapHistory();
                                showNotification();
                            });

                            window.addEventListener("pageshow", function (evt) {
                                if (evt.persisted) trapHistory();
                            });

                            document.addEventListener("livewire:navigated", function () {
                                trapHistory();
                            });

                            trapHistory();
                        })();
                    </script>
                '),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn () => new HtmlString(<<<'HTML'
                    <script>
                        function disableNativeValidation() {
                            document.querySelectorAll('form').forEach(function (form) {
                                form.setAttribute('novalidate', 'novalidate');
                            });
                        }

                        // Run on initial load
                        disableNativeValidation();

                        // Re-run after Livewire navigates or updates the DOM
                        document.addEventListener('livewire:navigated', disableNativeValidation);
                        document.addEventListener('livewire:load', disableNativeValidation);
                        document.addEventListener('livewire:update', disableNativeValidation);

                        // Catch-all: watch for new forms being added dynamically
                        const observer = new MutationObserver(disableNativeValidation);
                        observer.observe(document.body, { childList: true, subtree: true });
                    </script>
                HTML),
            )
            // ->renderHook(
            //     PanelsRenderHook::BODY_END,
            //     fn (): string => <<<'HTML'
            //         <script>
            //             function applyNovalidate() {
            //                 document.querySelectorAll('form').forEach((form) => {
            //                     form.setAttribute('novalidate', 'novalidate');
            //                 });
            //             }
    
            //             document.addEventListener('DOMContentLoaded', applyNovalidate);
            //             document.addEventListener('livewire:navigated', applyNovalidate);
            //             document.addEventListener('livewire:load', applyNovalidate);
    
            //             // Livewire re-renders forms on every update (validation errors, etc.)
            //             document.addEventListener('livewire:update', applyNovalidate);
            //             if (window.Livewire) {
            //                 Livewire.hook('morph.updated', ({ el }) => applyNovalidate());
            //             }
            //         </script>
            //     HTML,
            // )
            ->viteTheme('resources/css/filament/admin/theme.css')
            ;
    }
}
