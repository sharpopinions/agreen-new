<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\CustomerDocument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/** Документообіг клієнта: файли, які він бачить і завантажує в кабінеті. */
class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('admin.users.documents');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ! $ownerRecord->isStaff();
    }

    public static function types(): array
    {
        return collect(CustomerDocument::TYPES)->mapWithKeys(fn($t) => [$t => __('admin.users.doc_types.' . $t)])->all();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')->label(__('admin.users.doc_type'))
                ->options(static::types())->required()->default('invoice'),
            Forms\Components\TextInput::make('title')->label(__('admin.users.doc_title'))
                ->required()->maxLength(255)->placeholder('Рахунок-фактура №2026-04'),
            Forms\Components\Select::make('order_id')->label(__('admin.users.doc_order'))
                ->options(fn() => $this->getOwnerRecord()->orders()->latest('id')->pluck('number', 'id'))
                ->searchable(),
            Forms\Components\DatePicker::make('issued_at')->label(__('admin.users.doc_issued'))->default(now()),
            Forms\Components\FileUpload::make('path')->label(__('admin.users.doc_file'))
                ->disk(CustomerDocument::DISK)
                ->directory('customer-documents')
                ->visibility('private')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'application/zip',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                ->maxSize(20480)
                ->required()
                ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label(__('admin.users.doc_title'))->searchable(),
                Tables\Columns\TextColumn::make('type')->label(__('admin.users.doc_type'))
                    ->formatStateUsing(fn($state) => static::types()[$state] ?? $state)->badge()->color('gray'),
                Tables\Columns\TextColumn::make('order.number')->label(__('admin.users.doc_order'))->placeholder('—'),
                Tables\Columns\TextColumn::make('issued_at')->label(__('admin.users.doc_issued'))->date('d.m.Y'),
            ])
            ->defaultSort('id', 'desc')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(__('admin.users.download'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->action(fn(CustomerDocument $record) => Storage::disk(CustomerDocument::DISK)->download($record->path, $record->title . '.' . strtolower($record->extension()))),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
