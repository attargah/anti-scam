<?php

namespace Attargah\AntiScam\Resources\BlockedIpResource\Pages;


use Attargah\AntiScam\Resources\BlockedIpResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBlockedIps extends ManageRecords
{
    protected static string $resource = BlockedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->after(function ($record) {
                $record->logs()->create([
                    'ip_address'     => $record->ip_address,
                    'expires_at'     => $record->expires_at,
                    'form_identity' => 'Resource Form',
                    'reason'         => __('anti-scam::anti-scam.manual_add_reason'),
                    'user_agent'     => request()->userAgent(),
                    'request_url'    => url()->full(),
                    'request_path'   => request()->path(),
                    'request_method' => request()->method(),
                    'blocked_by'     => auth()->id(),
                ]);
            })
        ];
    }



}
