<?php

namespace App\Filament\Resources\MajorsResource\Pages;

use App\Filament\Resources\MajorsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMajor extends ViewRecord
{
    protected static string $resource = MajorsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
