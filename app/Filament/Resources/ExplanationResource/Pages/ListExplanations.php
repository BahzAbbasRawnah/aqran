<?php

namespace App\Filament\Resources\ExplanationResource\Pages;

use App\Filament\Resources\ExplanationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExplanations extends ListRecords
{
    protected static string $resource = ExplanationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('إضافة شرح'),
        ];
    }
}
