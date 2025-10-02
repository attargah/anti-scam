<?php

namespace Attargah\AntiScam\Resources;

use Attargah\AntiScam\Resources\BlockedIpLogResource\Pages;
use Attargah\AntiScam\Models\BlockedIpLog;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BlockedIpLogResource extends Resource
{

    protected static ?string $model = BlockedIpLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function canCreate(): bool
    {
        return false;
    }
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
        return __('anti-scam::anti-scam.blocked_ip_log.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('anti-scam::anti-scam.blocked_ip_log.model_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->required()
                    ->maxLength(45),

               Textarea::make('reason')
                    ->label(__('anti-scam::anti-scam.reason'))
                    ->rows(3),

                DateTimePicker::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->nullable()
                    ->helperText(__('anti-scam::anti-scam.expires_at_helper_text')),

                TextInput::make('form_identity')
                    ->label(__('anti-scam::anti-scam.form_identity'))
                    ->maxLength(255),

                 Textarea::make('user_agent')
                    ->label(__('anti-scam::anti-scam.user_agent'))
                    ->rows(2),

                 TextInput::make('request_url')
                    ->label(__('anti-scam::anti-scam.request_url'))
                    ->maxLength(200),

                TextInput::make('request_path')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->maxLength(100),

                TextInput::make('request_method')
                    ->label(__('anti-scam::anti-scam.request_method'))
                    ->maxLength(20),

                Select::make('blocked_by')
                    ->label(__('anti-scam::anti-scam.blocked_by'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),

                Select::make('blocked_ip_id')
                    ->label('Parent Blocked IP')
                    ->relationship('parent', 'ip_address')
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                 TextColumn::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reason')
                    ->label(__('anti-scam::anti-scam.reason'))
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('expires_at')
                    ->label(__('anti-scam::anti-scam.expires_at'))
                    ->default('Permanent')
                    ->toggleable(),

                TextColumn::make('form_identity')
                    ->label(__('anti-scam::anti-scam.form_identity'))
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('request_method')
                    ->label(__('anti-scam::anti-scam.request_method'))
                    ->sortable()
                    ->badge(),

                TextColumn::make('request_path')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label(__('anti-scam::anti-scam.blocked_by'))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('anti-scam::anti-scam.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                ViewAction::make(),
                DeleteAction::make()
            ])
            ->bulkActions([
                DeleteBulkAction::make()
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make(__('anti-scam::anti-scam.ip_information'))
                ->schema([
                    TextEntry::make('ip_address')
                        ->label(__('anti-scam::anti-scam.ip_address'))
                        ->columnSpanFull(),
                    TextEntry::make('reason')
                        ->label(__('anti-scam::anti-scam.reason'))
                        ->columnSpanFull(),
                    TextEntry::make('expires_at')
                        ->label(__('anti-scam::anti-scam.expires_at'))
                        ->columnSpanFull(),
                    TextEntry::make('form_identity')
                        ->label(__('anti-scam::anti-scam.form_identity'))
                        ->columnSpanFull(),
                    TextEntry::make('user_agent')
                        ->label(__('anti-scam::anti-scam.user_agent'))
                        ->columnSpanFull(),
                    TextEntry::make('request_url')
                        ->label(__('anti-scam::anti-scam.request_url'))
                        ->columnSpanFull(),
                    TextEntry::make('request_path')
                        ->label(__('anti-scam::anti-scam.request_path'))
                        ->columnSpanFull(),
                    TextEntry::make('request_method')
                        ->label(__('anti-scam::anti-scam.request_method'))
                        ->columnSpanFull(),
                    TextEntry::make('blocked_by.name')
                        ->label(__('anti-scam::anti-scam.blocked_by'))
                        ->columnSpanFull(),
                ])->columnSpanFull(),

        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBlockedIpLogs::route('/'),
        ];
    }
}
