<?php
namespace App\Filament\Resources\MajorsResource\Pages;
use App\Filament\Resources\MajorsResource;
use Filament\Resources\Pages\CreateRecord;
class CreateMajor extends CreateRecord
{
    protected static string $resource = MajorsResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
