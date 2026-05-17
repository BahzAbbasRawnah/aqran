<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TutoringRequestResource\Pages;
use App\Models\Explanation;
use App\Models\TutoringRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\ContentStatus;
use Illuminate\Support\Facades\DB;

class TutoringRequestResource extends Resource
{
    protected static ?string $model = TutoringRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationGroup = 'الإشراف والمراجعة';
    protected static ?string $navigationLabel = 'طلبات الشرح';
    protected static ?string $modelLabel = 'طلب شرح';
    protected static ?string $pluralModelLabel = 'طلبات الشرح';
    protected static ?int $navigationSort = 1;

    // ─── Read-only: no standard form (admin moderates only) ───────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ─── Infolist — rich view for a single record ────────────────────────────
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('بيانات الطالب والمقرر')
                ->schema([
                    Infolists\Components\TextEntry::make('student.user.name')
                        ->label('الطالب'),
                    Infolists\Components\TextEntry::make('course.name')
                        ->label('المقرر'),
                    Infolists\Components\TextEntry::make('course.code')
                        ->label('رمز المقرر')
                        ->badge(),
                    Infolists\Components\TextEntry::make('curriculum_parts')
                        ->label('أجزاء المنهج'),
                    Infolists\Components\IconEntry::make('is_completed')
                        ->label('هل أكمل المقرر؟')
                        ->boolean(),
                    Infolists\Components\TextEntry::make('grade')
                        ->label('الدرجة')
                        ->badge()
                        ->color('success')
                        ->visible(fn ($record) => $record->is_completed),
                ])->columns(2),

            Infolists\Components\Section::make('تفاصيل الطلب')
                ->schema([
                    Infolists\Components\TextEntry::make('learning_details')
                        ->label('كيف تعلّم المقرر؟')
                        ->columnSpanFull(),
                    Infolists\Components\TextEntry::make('status')
                        ->label('الحالة')
                        ->badge()
                        ->color(fn (ContentStatus $state): string => $state->color()),
                    Infolists\Components\TextEntry::make('reject_reason')
                        ->label('سبب الرفض')
                        ->hidden(fn ($record) => $record->status !== ContentStatus::REJECTED),
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

                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('الطالب')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('course.name')
                    ->label('المقرر')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('curriculum_parts')
                    ->label('أجزاء المنهج')
                    ->limit(30)
                    ->toggleable(),
 
                Tables\Columns\IconColumn::make('is_completed')
                    ->label('مكتمل')
                    ->boolean()
                    ->toggleable(),
 
                Tables\Columns\TextColumn::make('grade')
                    ->label('الدرجة')
                    ->badge()
                    ->color('success')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (ContentStatus $state): string => $state->color())
                    ->formatStateUsing(fn (ContentStatus $state): string => $state->label())
                    ->sortable(),

                Tables\Columns\IconColumn::make('video_url')
                    ->label('فيديو')
                    ->boolean()
                    ->trueIcon('heroicon-o-play-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ التقديم')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('تصفية حسب الحالة')
                    ->options([
                        'pending'  => 'قيد المراجعة',
                        'approved' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ]),
            ])
            ->actions([
                // ── 1. Video Preview Action ───────────────────────────────────
                Tables\Actions\Action::make('watch_video')
                    ->label('معاينة الفيديو')
                    ->icon('heroicon-o-play-circle')
                    ->color('info')
                    ->modalHeading('معاينة فيديو الشرح التجريبي')
                    ->modalContent(
                        fn (TutoringRequest $record) => view(
                            'filament.modals.video-preview',
                            ['url' => $record->video_url]
                        )
                    )
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق')
                    ->visible(fn (TutoringRequest $record): bool => !empty($record->video_url)),

                // ── 2. Approve Action ─────────────────────────────────────────
                Tables\Actions\Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد قبول الطلب')
                    ->modalDescription('هل أنت متأكد من قبول هذا الطلب؟')
                    ->modalSubmitActionLabel('نعم، قبول')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (TutoringRequest $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (TutoringRequest $record): void {
                        $record->update(['status' => ContentStatus::APPROVED]);

                        Notification::make()
                            ->title('✅ تم قبول الطلب')
                            ->body("تم قبول طلب الشرح بنجاح.")
                            ->success()
                            ->send();
                    }),

                // ── 3. Reject Action ──────────────────────────────────────────
                Tables\Actions\Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->modalHeading('رفض الطلب')
                    ->modalDescription('الرجاء إدخال سبب الرفض ليُرسل للطالب.')
                    ->form([
                        Forms\Components\Textarea::make('reject_reason')
                            ->label('سبب الرفض')
                            ->placeholder('أذكر سبباً واضحاً لمساعدة الطالب على التحسين...')
                            ->required()
                            ->rows(4),
                    ])
                    ->modalSubmitActionLabel('تأكيد الرفض')
                    ->modalCancelActionLabel('إلغاء')
                    ->visible(fn (TutoringRequest $record): bool => $record->status === ContentStatus::PENDING)
                    ->action(function (TutoringRequest $record, array $data): void {
                        $record->update([
                            'status'        => ContentStatus::REJECTED,
                            'reject_reason' => $data['reject_reason'],
                        ]);

                        Notification::make()
                            ->title('❌ تم رفض الطلب')
                            ->body("تم رفض الطلب مع توثيق السبب.")
                            ->danger()
                            ->send();
                    }),

                // ── 4. View details ────────────────────────────────────────────
                Tables\Actions\ViewAction::make()->label('التفاصيل'),
            ])
            ->bulkActions([])
            ->emptyStateHeading('لا توجد طلبات شرح')
            ->emptyStateIcon('heroicon-o-video-camera')
            ->poll('30s'); // Auto-refresh every 30 seconds for live moderation
    }

    public static function getRelations(): array
    {
        return [];
    }

    // Read-only: only List and View pages (no Create / Edit)
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTutoringRequests::route('/'),
            'view'  => Pages\ViewTutoringRequest::route('/{record}'),
        ];
    }
}
