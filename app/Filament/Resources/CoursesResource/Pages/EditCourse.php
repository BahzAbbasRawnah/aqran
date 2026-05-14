<?php
namespace App\Filament\Resources\CoursesResource\Pages;
use App\Filament\Resources\CoursesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditCourse extends EditRecord
{
    protected static string $resource = CoursesResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->label('حذف')];
    }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
