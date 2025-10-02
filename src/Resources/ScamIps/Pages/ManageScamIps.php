<?php

namespace Attargah\AntiScam\Resources\ScamIps\Pages;

use Attargah\AntiScam\Resources\ScamIps\ScamIpResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageScamIps extends ManageRecords
{
    protected static string $resource = ScamIpResource::class;
  

}
