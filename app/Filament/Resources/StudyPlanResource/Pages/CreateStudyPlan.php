<?php
namespace App\Filament\Resources\StudyPlanResource\Pages;
use App\Filament\Resources\StudyPlanResource;
use Filament\Resources\Pages\CreateRecord;
class CreateStudyPlan extends CreateRecord
{
    protected static string $resource = StudyPlanResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
