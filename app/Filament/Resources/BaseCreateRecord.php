<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Js;

class BaseCreateRecord extends CreateRecord
{

      protected function getCreatedNotification(): ?Notification
      {
            return Notification::make()
                  ->success()
                  ->title('ជោគជ័យ')
                  ->body('ទិន្នន័យត្រូវបានរក្សាទុកដោយជោគជ័យ')
                  ->send();
      }

      protected function getFormActions(): array
      {
            return [
                  $this->getCancelFormAction(),
                  $this->getSubmitFormAction(),
            ];
      }

      public function getFormActionsAlignment(): string|Alignment
      {
            return Alignment::End;
      }

      protected function getCancelFormAction(): Action
      {
            $url = $this->getResourceUrl();
            return Action::make('cancel')
                  ->label("ចាកចេញ")
                  ->color('danger')
                  ->alpineClickHandler(
                  FilamentView::hasSpaMode($url)
                        ? 'Livewire.navigate(' . Js::from($url) . ')'
                        : '(window.location.href = ' . Js::from($url) . ')',
                  );
      }

}