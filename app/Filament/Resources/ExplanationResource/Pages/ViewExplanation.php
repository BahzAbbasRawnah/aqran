<?php

namespace App\Filament\Resources\ExplanationResource\Pages;

use App\Filament\Resources\ExplanationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewExplanation extends ViewRecord
{
    protected static string $resource = ExplanationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()->label('تعديل'),
            Actions\DeleteAction::make()->label('حذف'),
        ];
    }
}
