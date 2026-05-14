<?php

namespace App\Filament\Resources\StudyPlanResource\Pages;

use App\Filament\Resources\StudyPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewStudyPlan extends ViewRecord
{
    protected static string $resource = StudyPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
