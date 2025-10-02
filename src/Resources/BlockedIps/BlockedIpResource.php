<?php

namespace Attargah\AntiScam\Resources\BlockedIps;



use Attargah\AntiScam\Models\BlockedIp;
use Attargah\AntiScam\Resources\BlockedIps\Pages\ManageBlockedIps;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;


class BlockedIpResource extends Resource
{
    protected static ?string $model = BlockedIp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    /**
     * @return string|null
     */
    public static function getNavigationGroup(): ?string
    {
        return __('anti-scam::anti-scam.navigation_group');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('anti-scam::anti-scam.blocked_ip.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('anti-scam::anti-scam.blocked_ip.model_label');
    }

    /**
     * @throws \Exception
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->required()
                    ->unique()
                    ->disabledOn('edit')
                    ->maxLength(45),

                DateTimePicker::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->nullable()
                    ->helperText(__('anti-scam::anti-scam.expires_at_helper_text'))
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('anti-scam::anti-scam.ip_information'))
                ->schema([
                    Section::make(__('anti-scam::anti-scam.ip_information'))
                        ->schema([
                            TextEntry::make('ip_address')
                                ->label(__('anti-scam::anti-scam.ip_address'))
                                ->columnSpanFull(),
                            TextEntry::make('expires_at')
                                ->label(__('anti-scam::anti-scam.expires_at'))
                                ->default(__('anti-scam::anti-scam.permanent'))
                                ->columnSpanFull(),
                        ])->columnSpanFull(),
                    Section::make(__('anti-scam::anti-scam.logs'))->schema([
                        RepeatableEntry::make('logs')
                            ->label(__('anti-scam::anti-scam.logs'))
                            ->schema([
                                Grid::make(2)->schema([
                                    TextEntry::make('ip_address')
                                        ->label(__('anti-scam::anti-scam.ip_address')),

                                    TextEntry::make('reason')
                                        ->label(__('anti-scam::anti-scam.reason')),
                                ]),

                                Grid::make(2)->schema([
                                    TextEntry::make('expires_at')
                                        ->label(__('anti-scam::anti-scam.expires_at')),

                                    TextEntry::make('form_identity')
                                        ->label(__('anti-scam::anti-scam.form_identity')),
                                ]),

                                Grid::make(2)->schema([
                                    TextEntry::make('user_agent')
                                        ->label(__('anti-scam::anti-scam.user_agent')),

                                    TextEntry::make('request_method')
                                        ->label(__('anti-scam::anti-scam.request_method')),
                                ]),

                                Grid::make(2)->schema([
                                    TextEntry::make('request_url')
                                        ->label(__('anti-scam::anti-scam.request_url')),

                                    TextEntry::make('request_path')
                                        ->label(__('anti-scam::anti-scam.request_path')),
                                ]),

                                TextEntry::make('blocked_by.name')
                                    ->label(__('anti-scam::anti-scam.blocked_by'))
                                    ->columnSpanFull(),
                            ])
                            ->columns(1)
                            ->contained(true),
                    ])->columnSpanFull(),
                ])->columnSpanFull(),
        ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->default(__('anti-scam::anti-scam.permanent'))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('anti-scam::anti-scam.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()->after(function ($record) {
                    $record->logs()->create([
                        'ip_address'     => $record->ip_address,
                        'expires_at'     => $record->expires_at,
                        'form_identity' => 'Resource Form',
                        'reason'         => __('anti-scam::anti-scam.edit_reason'),
                        'user_agent'     => request()->userAgent(),
                        'request_url'    => url()->full(),
                        'request_path'   => request()->path(),
                        'request_method' => request()->method(),
                        'blocked_by'     => auth()->id(),
                    ]);
                }),
                DeleteAction::make(),
            ])
            ->toolbarActions([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBlockedIps::route('/'),
        ];
    }
}
