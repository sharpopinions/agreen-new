<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin'];

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string { return __('admin.users.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.settings'); }
    public static function getModelLabel(): string { return __('admin.users.single'); }
    public static function getPluralModelLabel(): string { return __('admin.users.plural'); }

    public static function roleOptions(): array
    {
        return collect(array_merge(User::STAFF_ROLES, User::CUSTOMER_ROLES))
            ->mapWithKeys(fn($r) => [$r => __('admin.users.roles.' . $r)])
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin.fields.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')
                    ->label(__('admin.users.role'))
                    ->options(static::roleOptions())
                    ->required()
                    ->default('manager')
                    // Не можна змінити роль самому собі — щоб не втратити доступ
                    ->disabled(fn(?User $record) => $record?->is(auth()->user()))
                    ->dehydrated(fn(?User $record) => ! $record?->is(auth()->user()))
                    ->helperText(__('admin.users.role_hint')),
                Forms\Components\TextInput::make('password')
                    ->label(__('admin.users.password'))
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn(string $operation) => $operation === 'create')
                    ->dehydrated(fn($state) => filled($state))
                    ->helperText(fn(string $operation) => $operation === 'edit' ? __('admin.users.password_hint') : null),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('role')->label(__('admin.users.role'))
                    ->formatStateUsing(fn($state) => __('admin.users.roles.' . $state))
                    ->badge()
                    ->color(fn($state) => in_array($state, User::STAFF_ROLES, true) ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')->label(__('admin.users.role'))->options(static::roleOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return ! $record->is(auth()->user()) && parent::canDelete($record);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
