<x-filament-panels::page>
    <x-filament::section>
        {{ $this->form }}

        <div class="mt-4">
            @if (in_array($this->data['report_type'] ?? null, \App\Filament\Pages\Reports::EXCEL_REPORT_TYPES, true))
                <x-filament::button type="button" wire:click="downloadExcel" icon="heroicon-o-arrow-down-tray">
                    {{ __('global.report_download') }}
                </x-filament::button>
            @else
                <x-filament::button type="button" wire:click="openPrint" icon="heroicon-o-printer">
                    {{ __('global.report_generate') }}
                </x-filament::button>
            @endif
        </div>
    </x-filament::section>
</x-filament-panels::page>
