<?php

namespace App\Filament\Resources\StudyPlanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class StudyPlanCoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'studyPlanCourses';

    protected static ?string $title = 'المقررات والمستويات الدراسية';
    protected static ?string $modelLabel = 'مقرر في الخطة';
    protected static ?string $pluralModelLabel = 'مقررات الخطة';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('course_id')
                ->label('المقرر الدراسي')
                ->relationship('course', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => "[{$record->code}] {$record->name}"
                ),

            Forms\Components\TextInput::make('semester_level')
                ->label('المستوى الدراسي')
                ->helperText('المستوى من 1 إلى 10')
                ->required()
                ->numeric()
                ->minValue(1)
                ->maxValue(10),

            Forms\Components\Select::make('course_type')
                ->label('نوع المقرر')
                ->options([
                    'mandatory' => 'إجباري',
                    'elective'  => 'اختياري',
                ])
                ->required(),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course.name')
            ->columns([
                Tables\Columns\TextColumn::make('course.code')
                    ->label('رمز المقرر')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('اسم المقرر')
                    ->searchable(),

                Tables\Columns\TextColumn::make('semester_level')
                    ->label('المستوى الدراسي')
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('course_type')
                    ->label('نوع المقرر')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'mandatory' => 'danger',
                        'elective'  => 'success',
                        default   => 'gray',
                    }),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إضافة مقرر'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('semester_level', 'asc')
            ->emptyStateHeading('لا توجد مقررات في هذه الخطة')
            ->emptyStateDescription('أضف مقررات لهذه الخطة الدراسية.');
    }
}
