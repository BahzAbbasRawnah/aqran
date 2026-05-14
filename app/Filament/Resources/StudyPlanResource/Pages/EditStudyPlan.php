<?php
namespace App\Filament\Resources\StudyPlanResource\Pages;
use App\Filament\Resources\StudyPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditStudyPlan extends EditRecord
{
    protected static string $resource = StudyPlanResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()->label('حذف')];
    }
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
