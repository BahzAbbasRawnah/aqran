<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationLabel = 'الإعلانات';
    protected static ?string $modelLabel = 'إعلان';
    protected static ?string $pluralModelLabel = 'الإعلانات';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الإعلان')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان الإعلان')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('majors')
                        ->label('التخصصات المستهدفة')
                        ->relationship('majors', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('إعلان عام (لجميع التخصصات)'),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            ContentStatus::PENDING->value  => 'قيد المراجعة',
                            ContentStatus::APPROVED->value => 'منشور',
                            ContentStatus::REJECTED->value => 'مرفوض',
                        ])
                        ->required()
                        ->default(ContentStatus::APPROVED->value),

                    Forms\Components\RichEditor::make('content')
                        ->label('المحتوى')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('image_url')
                        ->label('صورة الإعلان')
                        ->image()
                        ->directory('announcements/images')
                        ->nullable()
                        ->columnSpanFull(),

                    Forms\Components\DateTimePicker::make('publish_date')
                        ->label('تاريخ النشر')
                        ->nullable()
                        ->displayFormat('Y/m/d H:i'),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('تاريخ الانتهاء')
                        ->nullable()
                        ->displayFormat('Y/m/d H:i'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان الإعلان')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('majors.name')
                    ->label('التخصصات')
                    ->badge()
                    ->color('primary')
                    ->default('عام'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (ContentStatus $state): string => $state->color())
                    ->formatStateUsing(fn (ContentStatus $state): string => $state->label()),

                Tables\Columns\TextColumn::make('publish_date')
                    ->label('تاريخ النشر')
                    ->dateTime('Y/m/d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('ينتهي في')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('تصفية حسب الحالة')
                    ->options([
                        'pending'  => 'قيد المراجعة',
                        'approved' => 'منشور',
                        'rejected' => 'مرفوض',
                    ]),
                Tables\Filters\SelectFilter::make('majors')
                    ->label('تصفية حسب التخصص')
                    ->relationship('majors', 'name')
                    ->multiple(),
            ])
            ->actions([
                // Quick Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('نشر')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('نشر الإعلان')
                    ->modalDescription('هل تريد نشر هذا الإعلان؟')
                    ->modalSubmitActionLabel('نعم، نشر')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Announcement $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Announcement $record): void {
                        $record->update(['status' => ContentStatus::APPROVED]);
                        Notification::make()
                            ->title('✅ تم نشر الإعلان')
                            ->success()
                            ->send();
                    }),

                // Quick Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('رفض الإعلان')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalSubmitActionLabel('تأكيد الرفض')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Announcement $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Announcement $record, array $data): void {
                        $record->update([
                            'status'        => ContentStatus::REJECTED,
                            'reject_reason' => $data['reject_reason'],
                        ]);
                        Notification::make()
                            ->title('❌ تم رفض الإعلان')
                            ->danger()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()->label('التفاصيل'),
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا توجد إعلانات')
            ->emptyStateIcon('heroicon-o-megaphone');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('بيانات الإعلان')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('عنوان الإعلان'),

                        Infolists\Components\TextEntry::make('majors.name')
                            ->label('التخصصات المستهدفة')
                            ->badge()
                            ->color('primary')
                            ->default('عام'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (ContentStatus $state): string => $state->color())
                            ->formatStateUsing(fn (ContentStatus $state): string => $state->label()),

                        Infolists\Components\TextEntry::make('publish_date')
                            ->label('تاريخ النشر')
                            ->dateTime('Y/m/d H:i'),

                        Infolists\Components\TextEntry::make('expires_at')
                            ->label('تاريخ الانتهاء')
                            ->dateTime('Y/m/d H:i')
                            ->default('مفتوح (لا ينتهي)'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('Y/m/d H:i'),

                        Infolists\Components\TextEntry::make('reject_reason')
                            ->label('سبب الرفض')
                            ->visible(fn (Announcement $record): bool => $record->status === ContentStatus::REJECTED)
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('المحتوى والصور')
                    ->schema([
                        Infolists\Components\TextEntry::make('content')
                            ->label('المحتوى')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\ImageEntry::make('image_url')
                            ->label('صورة الإعلان')
                            ->disk('public')
                            ->height(180)
                            ->width(320)
                            ->placeholder('لا توجد صورة مرفقة')
                            ->columnSpanFull(),
                    ])->columns(1),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'view'   => Pages\ViewAnnouncement::route('/{record}'),
            'edit'   => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
