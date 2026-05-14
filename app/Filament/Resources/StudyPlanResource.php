<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudyPlanResource\Pages;
use App\Filament\Resources\StudyPlanResource\RelationManagers;
use App\Models\StudyPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudyPlanResource extends Resource
{
    protected static ?string $model = StudyPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'الإدارة الأكاديمية';
    protected static ?string $navigationLabel = 'الخطط الدراسية';
    protected static ?string $modelLabel = 'خطة دراسية';
    protected static ?string $pluralModelLabel = 'الخطط الدراسية';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الخطة الدراسية')
                ->schema([
                    Forms\Components\Select::make('major_id')
                        ->label('التخصص')
                        ->relationship('major', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->label('اسم الخطة')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('مثال: خطة 2024'),

                    Forms\Components\TextInput::make('effective_year')
                        ->label('سنة التطبيق')
                        ->required()
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(2100)
                        ->placeholder('مثال: 2024'),
                ])
                ->columns(3),

            Forms\Components\Section::make('مواد الخطة الدراسية')
                ->schema([
                    Forms\Components\Repeater::make('studyPlanCourses')
                        ->relationship()
                        ->label('قائمة المواد')
                        ->addActionLabel('إضافة مادة جديدة')
                        ->schema([
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
                                ->label('المستوى')
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
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->collapsible()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الخطة')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('major.name')
                    ->label('التخصص')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('effective_year')
                    ->label('سنة التطبيق')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('الطلاب المسجلون')
                    ->counts('students')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('major_id')
                    ->label('تصفية حسب التخصص')
                    ->relationship('major', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('effective_year', 'desc')
            ->emptyStateHeading('لا توجد خطط دراسية بعد')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudyPlanCoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudyPlans::route('/'),
            'create' => Pages\CreateStudyPlan::route('/create'),
            'view'   => Pages\ViewStudyPlan::route('/{record}'),
            'edit'   => Pages\EditStudyPlan::route('/{record}/edit'),
        ];
    }
}
