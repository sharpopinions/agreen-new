<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictedToRoles;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/** Відгуки клієнтів — блок на сторінці «Послуги». */
class TestimonialResource extends Resource
{
    use RestrictedToRoles;

    protected static array $roles = ['admin', 'content'];

    protected static ?string $model = Testimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string { return __('admin.testimonials.plural'); }
    public static function getNavigationGroup(): ?string { return __('admin.groups.content'); }
    public static function getModelLabel(): string { return __('admin.testimonials.single'); }
    public static function getPluralModelLabel(): string { return __('admin.testimonials.plural'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('author')->label(__('admin.testimonials.author'))->required()->maxLength(255),
                Forms\Components\Select::make('rating')->label(__('admin.testimonials.rating'))
                    ->options([5 => '★★★★★', 4 => '★★★★', 3 => '★★★', 2 => '★★', 1 => '★'])->default(5)->required(),
                Forms\Components\Textarea::make('text')->label(__('admin.testimonials.text'))->required()->rows(4)->maxLength(1000)->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')->label(__('admin.fields.sort_order'))->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label(__('admin.fields.show_on_site'))->default(true)->inline(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('author')->label(__('admin.testimonials.author'))->searchable(),
                Tables\Columns\TextColumn::make('text')->label(__('admin.testimonials.text'))->limit(80)->wrap(),
                Tables\Columns\TextColumn::make('rating')->label(__('admin.testimonials.rating'))->formatStateUsing(fn($state) => str_repeat('★', (int) $state)),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.fields.show_on_site')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit'   => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
