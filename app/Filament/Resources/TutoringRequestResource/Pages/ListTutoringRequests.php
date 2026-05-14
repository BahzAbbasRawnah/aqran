<?php

namespace App\Filament\Resources\TutoringRequestResource\Pages;

use App\Filament\Resources\TutoringRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListTutoringRequests extends ListRecords
{
    protected static string $resource = TutoringRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No Create action — admin is read-only moderator
        ];
    }
}
