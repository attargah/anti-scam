<?php

namespace Attargah\AntiScam\Resources\BlockedIpLogResource\Pages;

use Attargah\AntiScam\Resources\BlockedIpLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlockedIpLogs extends ManageRecords
{
    protected static string $resource = BlockedIpLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
