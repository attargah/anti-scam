<?php

namespace Attargah\AntiScam\Resources;

use Attargah\AntiScam\Models\ScamIp;

use Attargah\AntiScam\Resources\ScamIpResource\Pages\ManageScamIp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class ScamIpResource extends Resource
{

    protected static ?string $model = ScamIp::class;
    protected static ?string $navigationIcon = 'heroicon-o-pause-circle';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('anti-scam.scam.register_logs_to_panel',false);
    }

    public static function getNavigationGroup(): ?string
    {
        return __('anti-scam::anti-scam.navigation_group');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('anti-scam::anti-scam.scam_ip.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('anti-scam::anti-scam.scam_ip.model_label');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->label(__('anti-scam::anti-scam.ip_address'))
                    ->required()
                    ->maxLength(45),

                Forms\Components\TextInput::make('form_identity')
                    ->label(__('anti-scam::anti-scam.form_identity'))
                    ->maxLength(255),

                Forms\Components\Textarea::make('user_agent')
                    ->label(__('anti-scam::anti-scam.user_agent'))
                    ->rows(2),

                Forms\Components\TextInput::make('request_url')
                    ->label(__('anti-scam::anti-scam.request_url'))
                    ->maxLength(200),

                Forms\Components\TextInput::make('request_path')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->maxLength(100),

                Forms\Components\TextInput::make('request_method')
                    ->label(__('anti-scam::anti-scam.request_method'))
                    ->maxLength(20),
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

                Tables\Columns\TextColumn::make('form_identity')
                    ->label(__('anti-scam::anti-scam.form_identity'))
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('request_method')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('request_path')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('anti-scam::anti-scam.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([

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
                ]),

        ]);
    }

        public static function getPages(): array
        {
            return [
                'index' => ManageScamIp::route('/'),
            ];
        }
}
