<?php

namespace Attargah\AntiScam\Resources;

use Attargah\AntiScam\Resources\BlockedIpResource\Pages;
use Attargah\AntiScam\Models\BlockedIp;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlockedIpResource extends Resource
{
    protected static ?string $model = BlockedIp::class;
    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';

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


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->disabledOn('edit')
                    ->maxLength(45),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->nullable()
                    ->helperText(__('anti-scam::anti-scam.expires_at_helper_text'))
            ]);


    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->default(__('anti-scam::anti-scam.permanent'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('anti-scam::anti-scam.created_at'))
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBlockedIps::route('/'),

        ];
    }
}
