<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityResource\Pages;
use App\Filament\Resources\CommunityResource\RelationManagers;
use App\Models\Community;
use App\Enums\ContentStatus;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CommunityResource extends Resource
{
    protected static ?string $model = Community::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'إدارة المحتوى';
    protected static ?string $navigationLabel = 'المجتمعات';
    protected static ?string $modelLabel = 'مجتمع';
    protected static ?string $pluralModelLabel = 'المجتمعات';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المجتمع')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم المجتمع')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('category')
                        ->label('الفئة')
                        ->maxLength(100)
                        ->placeholder('مثال: برمجة، شبكات، ذكاء اصطناعي'),
                    Forms\Components\Select::make('major_id')
                        ->label('التخصص المرتبط')
                        ->relationship('major', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->placeholder('مجتمع عام (لكافة التخصصات)'),
                    Forms\Components\TextInput::make('join_link')
                        ->label('رابط الانضمام')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://t.me/...'),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options([
                            ContentStatus::PENDING->value  => 'قيد المراجعة',
                            ContentStatus::APPROVED->value => 'نشط',
                            ContentStatus::REJECTED->value => 'مرفوض',
                        ])
                        ->required()
                        ->default(ContentStatus::APPROVED->value),
                    Forms\Components\Textarea::make('reject_reason')
                        ->label('سبب الرفض')
                        ->visible(fn (callable $get) => $get('status') === ContentStatus::REJECTED->value)
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->label('الوصف')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('cover_image')
                        ->label('صورة الغلاف')
                        ->image()
                        ->directory('communities/covers')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم المجتمع')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('الفئة')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('major.name')
                    ->label('التخصص')
                    ->badge()
                    ->color('info')
                    ->default('عام'),
                Tables\Columns\TextColumn::make('members_count')
                    ->counts('members')
                    ->label('الأعضاء')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (ContentStatus $state): string => $state->color())
                    ->formatStateUsing(fn (ContentStatus $state): string => $state->label()),
                Tables\Columns\TextColumn::make('join_link')
                    ->label('رابط الانضمام')
                    ->url(fn ($record) => $record->join_link)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                // Quick Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('تفعيل')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تفعيل المجتمع')
                    ->modalDescription('هل تريد تفعيل هذا المجتمع؟')
                    ->modalSubmitActionLabel('نعم، تفعيل')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Community $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Community $record): void {
                        $record->update(['status' => ContentStatus::APPROVED]);
                        Notification::make()
                            ->title('✅ تم تفعيل المجتمع')
                            ->success()
                            ->send();
                    }),

                // Quick Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('رفض المجتمع')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalSubmitActionLabel('تأكيد الرفض')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (Community $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (Community $record, array $data): void {
                        $record->update([
                            'status'        => ContentStatus::REJECTED,
                            'reject_reason' => $data['reject_reason'],
                        ]);
                        Notification::make()
                            ->title('❌ تم رفض المجتمع')
                            ->danger()
                            ->send();
                    }),

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
            ->emptyStateHeading('لا توجد مجتمعات بعد')
            ->emptyStateIcon('heroicon-o-user-group');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCommunities::route('/'),
            'create' => Pages\CreateCommunity::route('/create'),
            'view'   => Pages\ViewCommunity::route('/{record}'),
            'edit'   => Pages\EditCommunity::route('/{record}/edit'),
        ];
    }
}
