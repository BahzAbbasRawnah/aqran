<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkshopResource\Pages;
use App\Models\Workshop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class WorkshopResource extends Resource
{
    protected static ?string $model = Workshop::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationLabel = 'ورش العمل';
    protected static ?string $modelLabel = 'ورشة عمل';
    protected static ?string $pluralModelLabel = 'ورش العمل';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات الورشة')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('عنوان الورشة')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Select::make('user_id')
                        ->label('مقدم الورشة')
                        ->relationship('user', 'email')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->getOptionLabelFromRecordUsing(
                            fn ($record) => "{$record->first_name} {$record->last_name} ({$record->email})"
                        ),

                    Forms\Components\Select::make('targetMajors')
                        ->label('التخصصات المستهدفة')
                        ->relationship('targetMajors', 'name')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('لجميع التخصصات (عام)'),

                    Forms\Components\Select::make('target_audience_major_id')
                        ->label('التخصص المستهدف')
                        ->relationship('targetMajor', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('لجميع التخصصات'),

                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            ContentStatus::PENDING->value  => 'قيد المراجعة',
                            ContentStatus::APPROVED->value => 'مقبولة',
                            ContentStatus::REJECTED->value => 'مرفوضة',
                        ])
                        ->required()
                        ->required()
                        ->default(ContentStatus::APPROVED->value),

                    Forms\Components\RichEditor::make('description')
                        ->label('وصف الورشة')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('thumbnail_url')
                        ->label('الصورة المصغرة')
                        ->image()
                        ->directory('workshops/thumbnails')
                        ->nullable()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('video_url')
                        ->label('فيديو الورشة')
                        ->directory('workshops/videos')
                        ->acceptedFileTypes(['video/mp4', 'video/x-m4v', 'video/*'])
                        ->nullable()
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('عنوان الورشة')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('مقدم الورشة')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('targetMajors.name')
                    ->label('التخصصات المستهدفة')
                    ->badge()
                    ->color('success')
                    ->default('عام للجميع'),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (ContentStatus $state): string => $state->color())
                    ->formatStateUsing(fn (ContentStatus $state): string => $state->label()),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('تصفية حسب الحالة')
                    ->options([
                        'pending'  => 'قيد المراجعة',
                        'approved' => 'مقبولة',
                        'rejected' => 'مرفوضة',
                    ]),
                Tables\Filters\SelectFilter::make('targetMajors')
                    ->label('تصفية حسب التخصص')
                    ->relationship('targetMajors', 'name')
                    ->multiple(),
            ])
            ->actions([
                // Quick Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('قبول ورشة العمل')
                    ->modalDescription('هل أنت متأكد من قبول ونشر ورشة العمل هذه؟')
                    ->modalSubmitActionLabel('نعم، قبول')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Workshop $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Workshop $record): void {
                        $record->update(['status' => ContentStatus::APPROVED]);
                        Notification::make()
                            ->title('✅ تم قبول الورشة بنجاح')
                            ->success()
                            ->send();
                    }),

                // Quick Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('رفض الورشة')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalSubmitActionLabel('تأكيد الرفض')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Workshop $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Workshop $record, array $data): void {
                        $record->update([
                            'status'        => ContentStatus::REJECTED,
                            'reject_reason' => $data['reject_reason'],
                        ]);
                        Notification::make()
                            ->title('❌ تم رفض الورشة')
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
            ->emptyStateHeading('لا توجد ورش عمل')
            ->emptyStateIcon('heroicon-o-presentation-chart-bar');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('بيانات ورشة العمل')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('عنوان الورشة'),

                        Infolists\Components\TextEntry::make('user.name')
                            ->label('مقدم الورشة'),

                        Infolists\Components\TextEntry::make('targetMajors.name')
                            ->label('التخصصات المستهدفة')
                            ->badge()
                            ->color('success')
                            ->default('عام للجميع'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('الحالة')
                            ->badge()
                            ->color(fn (ContentStatus $state): string => $state->color())
                            ->formatStateUsing(fn (ContentStatus $state): string => $state->label()),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاريخ الإضافة')
                            ->dateTime('Y/m/d H:i'),

                        Infolists\Components\TextEntry::make('reject_reason')
                            ->label('سبب الرفض')
                            ->visible(fn (Workshop $record): bool => $record->status === ContentStatus::REJECTED)
                            ->columnSpanFull(),
                    ])->columns(2),

                Infolists\Components\Section::make('الوصف والمحتوى المرفق')
                    ->schema([
                        Infolists\Components\TextEntry::make('description')
                            ->label('وصف الورشة')
                            ->html()
                            ->columnSpanFull(),

                        Infolists\Components\ImageEntry::make('thumbnail_url')
                            ->label('الصورة المصغرة')
                            ->disk('public')
                            ->height(180)
                            ->width(320)
                            ->placeholder('لا توجد صورة مصغرة')
                            ->columnSpanFull(),

                        Infolists\Components\ViewEntry::make('video_url')
                            ->label('فيديو الورشة')
                            ->view('filament.infolists.video-player')
                            ->columnSpanFull()
                            ->visible(fn (Workshop $record): bool => !empty($record->video_url)),
                    ])->columns(1),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListWorkshops::route('/'),
            'create' => Pages\CreateWorkshop::route('/create'),
            'view'   => Pages\ViewWorkshop::route('/{record}'),
            'edit'   => Pages\EditWorkshop::route('/{record}/edit'),
        ];
    }
}
