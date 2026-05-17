<?php

namespace App\Filament\Resources\ExplanationResource\Pages;

use App\Filament\Resources\ExplanationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExplanation extends CreateRecord
{
    protected static string $resource = ExplanationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
