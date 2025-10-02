<?php

namespace Attargah\AntiScam\Resources\ScamIps;


use Attargah\AntiScam\Models\ScamIp;
use Attargah\AntiScam\Resources\ScamIps\Pages\ManageScamIps;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ScamIpResource extends Resource
{
    protected static ?string $model = ScamIp::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

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
                    ->disabled('edit')
                    ->maxLength(45),

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
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('anti-scam::anti-scam.ip_information'))
                ->schema([
                    TextEntry::make('ip_address')
                        ->label(__('anti-scam::anti-scam.ip_address'))
                        ->columnSpanFull(),
                    TextEntry::make('form_identity')
                        ->label(__('anti-scam::anti-scam.form_identity'))
                        ->columnSpanFull(),
                    TextEntry::make('reason')
                        ->label(__('anti-scam::anti-scam.reason'))
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

                TextColumn::make('form_identity')
                    ->label(__('anti-scam::anti-scam.form_identity'))
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('reason')
                    ->label(__('anti-scam::anti-scam.reason'))
                    ->toggleable(),

                TextColumn::make('request_method')
                    ->label(__('anti-scam::anti-scam.request_method'))
                    ->sortable()
                    ->badge(),

                TextColumn::make('request_path')
                    ->label(__('anti-scam::anti-scam.request_path'))
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label(__('anti-scam::anti-scam.created_at'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
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
            'index' => ManageScamIps::route('/'),
        ];
    }
}
