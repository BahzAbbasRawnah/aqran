<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExplanationResource\Pages;
use App\Models\Explanation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ExplanationResource extends Resource
{
    protected static ?string $model = Explanation::class;

    protected static ?string $navigationIcon = 'heroicon-o-film';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationLabel = 'شروحات المواد';
    protected static ?string $modelLabel = 'شرح';
    protected static ?string $pluralModelLabel = 'شروحات المواد';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات الشرح')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان الشرح')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('course_id')
                            ->label('المقرر')
                            ->relationship('course', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('user_id')
                            ->label('المشرح / الطالب')
                            ->relationship('user', 'email')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})"
                            ),

                        Forms\Components\TextInput::make('views')
                            ->label('عدد المشاهدات')
                            ->numeric()
                            ->default(0)
                            ->required(),

                        Forms\Components\FileUpload::make('video_path')
                            ->label('فيديو الشرح')
                            ->disk('public')
                            ->directory('explanations/videos')
                            ->acceptedFileTypes(['video/mp4', 'video/x-m4v', 'video/*'])
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('thumbnail_path')
                            ->label('الصورة المصغرة')
                            ->image()
                            ->disk('public')
                            ->directory('explanations/thumbnails')
                            ->nullable()
                            ->columnSpanFull(),
                    ])->columns(2),
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

                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان الشرح')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('المقرر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('المشرح / الطالب')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('views')
                    ->label('المشاهدات')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('تصفية حسب المقرر')
                    ->relationship('course', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // Quick watch video action
                Tables\Actions\Action::make('watch_video')
                    ->label('معاينة الفيديو')
                    ->icon('heroicon-o-play-circle')
                    ->color('info')
                    ->modalHeading('معاينة فيديو الشرح')
                    ->modalContent(
                        fn (Explanation $record) => view(
                            'filament.modals.video-preview',
                            ['url' => $record->video_url]
                        )
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق')
                    ->visible(fn (Explanation $record): bool => !empty($record->video_url)),

                Tables\Actions\ViewAction::make()->label('التفاصيل'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->emptyStateHeading('لا توجد شروحات بعد')
            ->emptyStateIcon('heroicon-o-film');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('بيانات الشرح')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('عنوان الشرح'),

                        Infolists\Components\TextEntry::make('course.name')
                            ->label('المقرر'),

                        Infolists\Components\TextEntry::make('course.code')
                            ->label('رمز المقرر')
                            ->badge(),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('المشرح / الطالب'),

                        Infolists\Components\TextEntry::make('views')
                            ->label('عدد المشاهدات')
                            ->badge()
                            ->color('success'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإضافة')
                            ->dateTime('Y/m/d H:i'),
                    ])->columns(2),

                Infolists\Components\Section::make('الملفات المرفقة والمعاينة')
                    ->schema([
                        Infolists\Components\ImageEntry::make('thumbnail_path')
                            ->label('الصورة المصغرة')
                            ->disk('public')
                            ->height(180)
                            ->width(320)
                            ->placeholder('لا توجد صورة مصغرة')
                            ->columnSpanFull(),

                        Infolists\Components\ViewEntry::make('video_url')
                            ->label('فيديو الشرح')
                            ->view('filament.infolists.video-player')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExplanations::route('/'),
            'create' => Pages\CreateExplanation::route('/create'),
            'view' => Pages\ViewExplanation::route('/{record}'),
            'edit' => Pages\EditExplanation::route('/{record}/edit'),
        ];
    }
}
