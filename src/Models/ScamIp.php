<?php

namespace Attargah\AntiScam\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScamIp extends Model
{
    protected $table = 'scam_ips';

    protected $fillable = [
        'ip_address',
        'form_identity',
        'reason',
        'user_agent',
        'request_url',
        'request_path',
        'request_method',
    
    ];

    


}
