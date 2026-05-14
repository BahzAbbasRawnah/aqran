<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoursesResource\Pages;
use App\Models\Course;
use App\Models\Major;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CoursesResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'الإدارة الأكاديمية';
    protected static ?string $navigationLabel = 'المقررات الدراسية';
    protected static ?string $modelLabel = 'مقرر';
    protected static ?string $pluralModelLabel = 'المقررات الدراسية';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المقرر')
                ->schema([
                    Forms\Components\Select::make('major_id')
                        ->label('التخصص')
                        ->relationship('major', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('code')
                        ->label('رمز المقرر')
                        ->required()
                        ->maxLength(20)
                        ->placeholder('مثال: CS101'),

                    Forms\Components\TextInput::make('name')
                        ->label('اسم المقرر')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('مثال: مقدمة في البرمجة'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('الرمز')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المقرر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('major.name')
                    ->label('التخصص')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

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
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('code', 'asc')
            ->emptyStateHeading('لا توجد مقررات بعد')
            ->emptyStateIcon('heroicon-o-book-open');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit'   => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
