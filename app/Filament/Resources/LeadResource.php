<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/** Заявки з форм сайту. Створює лише сайт; менеджер змінює статус і пише коментар. */
class LeadResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'manager'];

    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string { return __('admin.leads.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.sales'); }
    public static function getModelLabel(): string { return __('admin.leads.single'); }
    public static function getPluralModelLabel(): string { return __('admin.leads.plural'); }

    public static function getNavigationBadge(): ?string
    {
        $new = Lead::where('status', 'new')->count();

        return $new ? (string) $new : null;
    }

    public static function canCreate(): bool { return false; }

    public static function types(): array
    {
        return collect(Lead::TYPES)->mapWithKeys(fn($t) => [$t => __('admin.leads.types.' . $t)])->all();
    }

    public static function statuses(): array
    {
        return collect(Lead::STATUSES)->mapWithKeys(fn($s) => [$s => __('admin.leads.statuses.' . $s)])->all();
    }

    public static function statusColor(?string $status): string
    {
        return match ($status) {
            'new' => 'info', 'in_progress' => 'warning', 'done' => 'success', default => 'gray',
        };
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\Select::make('status')
                    ->label(__('admin.leads.status'))
                    ->options(self::statuses())
                    ->required(),
                Forms\Components\Textarea::make('manager_note')
                    ->label(__('admin.leads.manager_note'))
                    ->rows(4)
                    ->maxLength(5000),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make()->schema([
                Infolists\Components\TextEntry::make('type')->label(__('admin.leads.type'))
                    ->formatStateUsing(fn($state) => self::types()[$state] ?? $state)->badge()->color('gray'),
                Infolists\Components\TextEntry::make('status')->label(__('admin.leads.status'))
                    ->formatStateUsing(fn($state) => self::statuses()[$state] ?? $state)->badge()
                    ->color(fn($state) => self::statusColor($state)),
                Infolists\Components\TextEntry::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i'),
                Infolists\Components\TextEntry::make('manager.name')->label(__('admin.leads.manager'))->placeholder('—'),
            ])->columns(4),

            Infolists\Components\Section::make(__('admin.leads.contact'))->schema([
                Infolists\Components\TextEntry::make('name')->label(__('admin.orders.name')),
                Infolists\Components\TextEntry::make('phone')->label(__('admin.orders.phone'))->placeholder('—')
                    ->url(fn(Lead $record) => $record->phone ? 'tel:' . preg_replace('/[^\d+]/', '', $record->phone) : null),
                Infolists\Components\TextEntry::make('email')->label('Email')->placeholder('—')
                    ->url(fn(Lead $record) => $record->email ? 'mailto:' . $record->email : null),
                Infolists\Components\TextEntry::make('company')->label(__('admin.orders.company'))->placeholder('—'),
            ])->columns(4),

            Infolists\Components\Section::make(__('admin.leads.request'))->schema([
                Infolists\Components\TextEntry::make('product')
                    ->label(__('admin.resources.product'))
                    ->getStateUsing(fn(Lead $record) => $record->product
                        ? $record->product->sku . ' — ' . ($record->product->translations()->value('name') ?? '')
                        : null)
                    ->url(fn(Lead $record) => $record->product ? ProductResource::getUrl('edit', ['record' => $record->product]) : null)
                    ->placeholder('—'),
                Infolists\Components\KeyValueEntry::make('payload_view')
                    ->label(__('admin.leads.details'))
                    ->getStateUsing(fn(Lead $record) => collect((array) $record->payload)
                        ->mapWithKeys(fn($v, $k) => [__('admin.leads.payload.' . $k) => $k === 'partner_type' ? __('admin.leads.partner_types.' . $v) : $v])
                        ->all())
                    ->hidden(fn(Lead $record) => empty($record->payload)),
                Infolists\Components\TextEntry::make('message')->label(__('admin.orders.comment'))->placeholder('—')->columnSpanFull(),
                Infolists\Components\TextEntry::make('source_url')->label(__('admin.leads.source'))->placeholder('—')
                    ->url(fn($state) => $state)->openUrlInNewTab()->columnSpanFull(),
                Infolists\Components\TextEntry::make('manager_note')->label(__('admin.leads.manager_note'))->placeholder('—')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.orders.created_at'))->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('type')->label(__('admin.leads.type'))
                    ->formatStateUsing(fn($state) => self::types()[$state] ?? $state)->badge()->color('gray'),
                Tables\Columns\TextColumn::make('status')->label(__('admin.leads.status'))
                    ->formatStateUsing(fn($state) => self::statuses()[$state] ?? $state)->badge()
                    ->color(fn($state) => self::statusColor($state)),
                Tables\Columns\TextColumn::make('name')->label(__('admin.leads.contact'))
                    ->description(fn(Lead $record) => collect([$record->phone, $record->email])->filter()->implode(' · '))
                    ->searchable(['name', 'phone', 'email', 'company']),
                Tables\Columns\TextColumn::make('product.sku')->label('SKU')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('message')->label(__('admin.orders.comment'))->limit(60)->placeholder('—')->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->label(__('admin.leads.status'))->options(self::statuses())->multiple()
                    ->default(['new', 'in_progress']),
                Tables\Filters\SelectFilter::make('type')->label(__('admin.leads.type'))->options(self::types())->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('take')
                    ->label(__('admin.leads.take'))
                    ->icon('heroicon-o-hand-raised')
                    ->visible(fn(Lead $record) => $record->status === 'new')
                    ->action(fn(Lead $record) => $record->update(['status' => 'in_progress', 'manager_id' => auth()->id()])),
                Tables\Actions\EditAction::make()->label(__('admin.orders.process')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('spam')->label(__('admin.leads.mark_spam'))->icon('heroicon-o-no-symbol')
                        ->action(fn($records) => $records->each->update(['status' => 'spam']))->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'view'  => Pages\ViewLead::route('/{record}'),
            'edit'  => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['product', 'manager']);
    }
}
