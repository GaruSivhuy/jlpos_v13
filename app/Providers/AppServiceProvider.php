<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentColor::register([
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
        ]);

        $this->registerAdvanceFieldValidating();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    public function registerAdvanceFieldValidating(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): View => view('filament.forms.components.advance-field-validating'),
        );
    }
}
