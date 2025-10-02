<?php

namespace Attargah\AntiScam\Resources\BlockedIpLogs\Pages;

use Attargah\AntiScam\Resources\BlockedIpLogs\BlockedIpLogResource;

use Filament\Resources\Pages\ManageRecords;

class ManageBlockedIpLogs extends ManageRecords
{
    protected static string $resource = BlockedIpLogResource::class;

}
