<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MajorsResource\Pages;
use App\Filament\Resources\MajorsResource\RelationManagers;
use App\Models\Major;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MajorsResource extends Resource
{
    protected static ?string $model = Major::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'الإدارة الأكاديمية';
    protected static ?string $navigationLabel = 'التخصصات';
    protected static ?string $modelLabel = 'تخصص';
    protected static ?string $pluralModelLabel = 'التخصصات';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات التخصص')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم التخصص')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('مثال: علوم الحاسوب'),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم التخصص')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('students_count')
                    ->label('عدد الطلاب')
                    ->counts('students')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('study_plans_count')
                    ->label('الخطط الدراسية')
                    ->counts('studyPlans')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
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
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('لا توجد تخصصات بعد')
            ->emptyStateDescription('ابدأ بإضافة أول تخصص.')
            ->emptyStateIcon('heroicon-o-academic-cap');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\StudentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMajors::route('/'),
            'create' => Pages\CreateMajor::route('/create'),
            'view'   => Pages\ViewMajor::route('/{record}'),
            'edit'   => Pages\EditMajor::route('/{record}/edit'),
        ];
    }
}
