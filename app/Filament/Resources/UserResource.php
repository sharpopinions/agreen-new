<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use App\Models\PaymentProvider;
use App\Models\ShippingProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager'];

    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string { return __('admin.users.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.settings'); }
    public static function getModelLabel(): string { return __('admin.users.single'); }
    public static function getPluralModelLabel(): string { return __('admin.users.plural'); }

    public static function roleOptions(): array
    {
        // Менеджер працює лише з клієнтами — ролі персоналу призначає адміністратор
        $roles = auth()->user()?->role === 'admin' ? array_merge(User::STAFF_ROLES, User::CUSTOMER_ROLES) : User::CUSTOMER_ROLES;

        return collect($roles)
            ->mapWithKeys(fn($r) => [$r => __('admin.users.roles.' . $r)])
            ->all();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Placeholder::make('request')
                ->hiddenLabel()
                ->content(fn(?User $record) => __('admin.users.request_hint', [
                    'role' => __('admin.users.roles.' . $record?->requested_role),
                    'date' => $record?->role_requested_at?->format('d.m.Y H:i'),
                ]))
                ->visible(fn(?User $record) => filled($record?->requested_role))
                ->extraAttributes(['style' => 'padding:12px 16px;border-radius:12px;border:1px solid #fde68a;background:#fffbeb;color:#b45309'])
                ->columnSpanFull(),

            Forms\Components\Section::make(__('admin.users.section_account'))->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin.users.first_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label(__('admin.users.last_name'))
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('phone')
                    ->label(__('admin.users.phone'))
                    ->tel()
                    ->maxLength(20)
                    ->dehydrateStateUsing(fn($state) => User::normalizePhone($state))
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('role')
                    ->label(__('admin.users.role'))
                    ->options(static::roleOptions())
                    ->required()
                    ->default(fn() => auth()->user()?->role === 'admin' ? 'manager' : 'client')
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
                Forms\Components\Placeholder::make('meta')
                    ->hiddenLabel()
                    ->content(fn(?User $record) => collect([
                        $record?->email_verified_at ? '✓ ' . __('admin.users.verified') . ' ' . $record->email_verified_at->format('d.m.Y') : null,
                        $record?->last_login_at ? __('admin.users.last_login') . ': ' . $record->last_login_at->format('d.m.Y H:i') : null,
                    ])->filter()->implode(' · '))
                    ->visible(fn(?User $record) => $record !== null)
                    ->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make(__('admin.users.section_company'))->schema([
                Forms\Components\TextInput::make('company')->label(__('admin.users.company'))->maxLength(255),
                Forms\Components\TextInput::make('company_code')->label(__('admin.users.company_code'))->maxLength(20),
            ])->columns(2)
                ->hidden(fn(Forms\Get $get) => in_array($get('role'), User::STAFF_ROLES, true)),

            Forms\Components\Section::make(__('admin.users.section_defaults'))->schema([
                Forms\Components\Select::make('default_shipping_provider_id')->label(__('admin.users.default_delivery'))
                    ->options(fn() => ShippingProvider::available()->pluck('name', 'id')),
                Forms\Components\Select::make('default_payment_provider_id')->label(__('admin.users.default_payment'))
                    ->options(fn() => PaymentProvider::availableFor(null)->pluck('name', 'id')),
                Forms\Components\TextInput::make('default_city')->label(__('admin.users.default_city'))->maxLength(255),
                Forms\Components\TextInput::make('default_address')->label(__('admin.users.default_address'))->maxLength(255),
            ])->columns(2)->collapsible()->collapsed()
                ->hidden(fn(Forms\Get $get) => in_array($get('role'), User::STAFF_ROLES, true)),
        ]);
    }

    /** Дії «Підтвердити / Відхилити заявку» (таблиця й сторінка редагування). */
    public static function requestActions(string $class, bool $compact = false): array
    {
        return [
            $class::make('approve')
                ->label(__('admin.users.approve'))
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->when($compact, fn($action) => $action->iconButton()->tooltip($action->getLabel()))
                ->requiresConfirmation()
                ->visible(fn(User $record) => filled($record->requested_role))
                ->action(function (User $record) {
                    $record->approveBusinessRole();
                    Notification::make()->success()->title(__('admin.users.approved'))->send();
                }),
            $class::make('reject')
                ->label(__('admin.users.reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->when($compact, fn($action) => $action->iconButton()->tooltip($action->getLabel()))
                ->requiresConfirmation()
                ->visible(fn(User $record) => filled($record->requested_role))
                ->action(function (User $record) {
                    $record->rejectBusinessRole();
                    Notification::make()->title(__('admin.users.rejected'))->send();
                }),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('admin.fields.name'))
                    ->formatStateUsing(fn(User $record) => $record->fullName())
                    ->description(fn(User $record) => $record->company)
                    ->searchable(['name', 'last_name', 'company']),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable(),
                Tables\Columns\TextColumn::make('phone')->label(__('admin.users.phone'))->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('role')->label(__('admin.users.role'))
                    ->formatStateUsing(fn($state) => __('admin.users.roles.' . $state))
                    ->badge()
                    ->color(fn($state) => in_array($state, User::STAFF_ROLES, true) ? 'warning' : (in_array($state, User::BUSINESS_ROLES, true) ? 'success' : 'gray')),
                Tables\Columns\TextColumn::make('requested_role')->label(__('admin.users.requested_role'))
                    ->formatStateUsing(fn($state) => __('admin.users.request_badge') . ': ' . __('admin.users.roles.' . $state))
                    ->badge()->color('danger')->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')->label(__('admin.users.role'))->options(static::roleOptions()),
                Tables\Filters\Filter::make('has_request')->label(__('admin.users.has_request'))
                    ->query(fn(Builder $q) => $q->whereNotNull('requested_role'))->toggle(),
            ])
            ->actions([
                ...static::requestActions(Tables\Actions\Action::class, compact: true),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn(User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function canDelete(Model $record): bool
    {
        return ! $record->is(auth()->user()) && parent::canDelete($record);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        return auth()->user()?->role === 'admin' ? $query : $query->whereIn('role', User::CUSTOMER_ROLES);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = User::whereNotNull('requested_role')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\CashbackRelationManager::class,
        ];
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
