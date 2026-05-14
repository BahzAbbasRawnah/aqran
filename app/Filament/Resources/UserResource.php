<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'إدارة المستخدمين';
    protected static ?string $navigationLabel = 'المستخدمون';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $pluralModelLabel = 'المستخدمون';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('البيانات الشخصية')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('الاسم الأول')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('last_name')
                        ->label('الاسم الأخير')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('username')
                        ->label('اسم المستخدم')
                        ->required()
                        ->maxLength(50)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true),
                ])->columns(2),

            Forms\Components\Section::make('الصلاحيات والأمان')
                ->schema([
                    Forms\Components\Select::make('role')
                        ->label('الدور')
                        ->options(['student' => 'طالب', 'admin' => 'مدير النظام'])
                        ->required()
                        ->default('student'),
                    Forms\Components\DatePicker::make('birth_date')
                        ->label('تاريخ الميلاد')
                        ->nullable(),
                    Forms\Components\TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->revealable()
                        ->nullable()
                        ->minLength(8)
                        ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null)
                        ->dehydrated(fn ($state) => !empty($state))
                        ->hint('اتركها فارغة لعدم التغيير'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable()->width(60),
                Tables\Columns\TextColumn::make('username')->label('اسم المستخدم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم الكامل')->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('email')->label('البريد الإلكتروني')->searchable()->copyable()->toggleable(),
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
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime('Y/m/d')->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('تصفية حسب الدور')
                    ->options(['student' => 'طالب', 'admin' => 'مدير النظام']),
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
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('لا يوجد مستخدمون')
            ->emptyStateIcon('heroicon-o-users');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
