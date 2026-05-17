<?php

namespace App\Filament\Resources\ExplanationResource\Pages;

use App\Filament\Resources\ExplanationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExplanation extends EditRecord
{
    protected static string $resource = ExplanationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
