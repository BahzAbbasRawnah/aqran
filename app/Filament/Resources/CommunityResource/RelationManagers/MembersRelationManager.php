<?php

namespace App\Filament\Resources\CommunityResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'أعضاء المجتمع';
    protected static ?string $modelLabel = 'عضو';
    protected static ?string $pluralModelLabel = 'الأعضاء';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('المستخدم')
                ->relationship('user', 'email')
                ->searchable()
                ->preload()
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('email')
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('اسم المستخدم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم الكامل')
                    ->searchable(['first_name', 'last_name']),

                Tables\Columns\TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('role')
                    ->label('الدور')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin'   => 'danger',
                        'student' => 'success',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin'   => 'مدير',
                        'student' => 'طالب',
                        default   => $state,
                    }),

                Tables\Columns\TextColumn::make('joined_at')
                    ->label('تاريخ الانضمام')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('إضافة عضو')
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('إزالة'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('إزالة المحدد'),
                ]),
            ])
            ->emptyStateHeading('لا يوجد أعضاء في هذا المجتمع')
            ->emptyStateIcon('heroicon-o-users');
    }
}
