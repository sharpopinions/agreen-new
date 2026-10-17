<?php

namespace App\Filament\Support;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

/**
 * Конструктор сторінки: набір блоків з дизайну (Claude Design → extra-pages.jsx).
 * Ті самі блоки рендерить resources/js/Components/Blocks/*.
 */
class PageBlocks
{
    private const RICH_TOOLBAR = ['bold', 'italic', 'h2', 'h3', 'bulletList', 'orderedList', 'link', 'undo', 'redo'];

    public static function make(string $name): Builder
    {
        return Builder::make($name)
            ->label(__('admin.pages.blocks'))
            ->blocks([
                Block::make('section')
                    ->label(__('admin.pages.block.section'))
                    ->icon('heroicon-o-list-bullet')
                    ->schema([
                        TextInput::make('title')->label(__('admin.pages.heading'))->required()->maxLength(255),
                        RichEditor::make('html')->label(__('admin.pages.text'))->toolbarButtons(self::RICH_TOOLBAR)->required(),
                    ]),
                Block::make('text')
                    ->label(__('admin.pages.block.text'))
                    ->icon('heroicon-o-bars-3-bottom-left')
                    ->schema([
                        RichEditor::make('html')->hiddenLabel()->toolbarButtons(self::RICH_TOOLBAR)->required(),
                    ]),
                Block::make('image')
                    ->label(__('admin.pages.block.image'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        FileUpload::make('path')->hiddenLabel()->disk('public')->directory('pages')->image()->imageEditor()->maxSize(5120),
                        TextInput::make('alt')->label(__('admin.fields.alt'))->maxLength(255),
                    ]),
                Block::make('split')
                    ->label(__('admin.pages.block.split'))
                    ->icon('heroicon-o-rectangle-group')
                    ->schema([
                        TextInput::make('heading')->label(__('admin.pages.heading'))->maxLength(255),
                        RichEditor::make('html')->label(__('admin.pages.text'))->toolbarButtons(self::RICH_TOOLBAR),
                        TextInput::make('list_heading')->label(__('admin.pages.list_heading'))->maxLength(255),
                        Repeater::make('items')->label(__('admin.pages.checklist'))->simple(
                            TextInput::make('text')->required()->maxLength(255),
                        )->defaultItems(0)->addActionLabel(__('admin.pages.add_item')),
                        FileUpload::make('path')->label(__('admin.fields.image'))->disk('public')->directory('pages')->image()->imageEditor()->maxSize(5120),
                        TextInput::make('alt')->label(__('admin.fields.alt'))->maxLength(255),
                    ]),
                Block::make('lead_form')
                    ->label(__('admin.pages.block.lead_form'))
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->schema([
                        Select::make('form')->label(__('admin.pages.form_type'))
                            ->options([
                                'partner' => __('admin.leads.types.partner'),
                                'service' => __('admin.leads.types.service'),
                                'contact' => __('admin.leads.types.contact'),
                            ])
                            ->default('partner')->required(),
                        TextInput::make('heading')->label(__('admin.pages.heading'))->maxLength(255),
                        TextInput::make('options_heading')->label(__('admin.pages.options_heading'))->maxLength(255),
                        Repeater::make('options')->label(__('admin.pages.form_options'))
                            ->helperText(__('admin.pages.form_options_hint'))
                            ->schema([
                                TextInput::make('label')->label(__('admin.pages.heading'))->required()->maxLength(100),
                                Textarea::make('text')->label(__('admin.pages.text'))->rows(2)->maxLength(500),
                            ])->columns(2)->defaultItems(0)->collapsible()->addActionLabel(__('admin.pages.add_item')),
                        TextInput::make('perks_heading')->label(__('admin.pages.list_heading'))->maxLength(255),
                        Repeater::make('perks')->label(__('admin.pages.checklist'))->simple(
                            TextInput::make('text')->required()->maxLength(255),
                        )->defaultItems(0)->addActionLabel(__('admin.pages.add_item')),
                    ]),
                Block::make('intro_stats')
                    ->label(__('admin.pages.block.intro_stats'))
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        TextInput::make('heading')->label(__('admin.pages.heading'))->maxLength(255),
                        RichEditor::make('html')->label(__('admin.pages.text'))->toolbarButtons(self::RICH_TOOLBAR),
                        Repeater::make('stats')->label(__('admin.pages.stats'))->schema([
                            TextInput::make('value')->label(__('admin.pages.stat_value'))->required()->maxLength(20),
                            TextInput::make('label')->label(__('admin.pages.stat_label'))->required()->maxLength(100),
                        ])->columns(2)->defaultItems(0)->maxItems(6)->addActionLabel(__('admin.pages.add_stat')),
                    ]),
                Block::make('features')
                    ->label(__('admin.pages.block.features'))
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        TextInput::make('heading')->label(__('admin.pages.heading'))->maxLength(255),
                        Repeater::make('items')->hiddenLabel()->schema([
                            TextInput::make('title')->label(__('admin.pages.heading'))->required()->maxLength(255),
                            Textarea::make('text')->label(__('admin.pages.text'))->rows(2)->maxLength(500),
                        ])->columns(2)->defaultItems(0)->collapsible()->addActionLabel(__('admin.pages.add_item')),
                    ]),
                Block::make('columns')
                    ->label(__('admin.pages.block.columns'))
                    ->icon('heroicon-o-view-columns')
                    ->schema([
                        Repeater::make('columns')->hiddenLabel()->schema([
                            TextInput::make('heading')->label(__('admin.pages.heading'))->required()->maxLength(255),
                            Repeater::make('items')->label(__('admin.pages.items'))->schema([
                                TextInput::make('title')->label(__('admin.pages.heading'))->required()->maxLength(255),
                                Textarea::make('text')->label(__('admin.pages.text'))->rows(2)->maxLength(500),
                            ])->defaultItems(0)->collapsible()->addActionLabel(__('admin.pages.add_item')),
                        ])->grid(2)->defaultItems(2)->maxItems(3)->addActionLabel(__('admin.pages.add_column')),
                    ]),
                Block::make('brands')
                    ->label(__('admin.pages.block.brands'))
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        TextInput::make('heading')->label(__('admin.pages.heading'))->maxLength(255)
                            ->helperText(__('admin.pages.brands_hint')),
                    ]),
                Block::make('cta')
                    ->label(__('admin.pages.block.cta'))
                    ->icon('heroicon-o-megaphone')
                    ->schema([
                        TextInput::make('heading')->label(__('admin.pages.heading'))->required()->maxLength(255),
                        TextInput::make('text')->label(__('admin.pages.text'))->maxLength(500),
                        Repeater::make('buttons')->label(__('admin.pages.buttons'))->schema([
                            TextInput::make('label')->label(__('admin.pages.button_label'))->required()->maxLength(60),
                            TextInput::make('url')->label(__('admin.pages.button_url'))->required()->maxLength(255)->placeholder('/contacts'),
                            Select::make('style')->label(__('admin.pages.button_style'))
                                ->options(['primary' => __('admin.pages.style_primary'), 'outline' => __('admin.pages.style_outline')])
                                ->default('primary')->required(),
                        ])->columns(3)->defaultItems(1)->maxItems(3)->addActionLabel(__('admin.pages.add_button')),
                    ]),
            ])
            ->blockNumbers(false)
            ->collapsible()
            ->cloneable()
            ->addActionLabel(__('admin.pages.add_block'))
            ->columnSpanFull();
    }
}
