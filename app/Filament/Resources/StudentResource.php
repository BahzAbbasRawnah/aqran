<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'إدارة المستخدمين';
    protected static ?string $navigationLabel = 'الطلاب';
    protected static ?string $modelLabel = 'طالب';
    protected static ?string $pluralModelLabel = 'الطلاب';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الطالب')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('المستخدم')
                        ->relationship('user', 'email')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})"
                        ),

                    Forms\Components\Select::make('major_id')
                        ->label('التخصص')
                        ->relationship('major', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\Select::make('study_plan_id')
                        ->label('الخطة الدراسية')
                        ->relationship('studyPlan', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Forms\Components\TextInput::make('enrollment_year')
                        ->label('سنة الالتحاق')
                        ->required()
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(2100),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم الطالب')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('major.name')
                    ->label('التخصص')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('studyPlan.name')
                    ->label('الخطة الدراسية')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->default('غير محدد'),

                Tables\Columns\TextColumn::make('enrollment_year')
                    ->label('سنة الالتحاق')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التسجيل')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('major_id')
                    ->label('تصفية حسب التخصص')
                    ->relationship('major', 'name'),

                Tables\Filters\SelectFilter::make('enrollment_year')
                    ->label('تصفية حسب سنة الالتحاق')
                    ->options(
                        Student::query()
                            ->distinct()
                            ->pluck('enrollment_year', 'enrollment_year')
                            ->toArray()
                    ),
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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا يوجد طلاب مسجلون')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
