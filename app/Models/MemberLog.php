<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLog extends Model
{
    protected $table = 'member_logs';

    // created_at のみ運用（updated_at は持たない）
    const UPDATED_AT = null;

    protected $fillable = [
        'member_id',
        'action',
        'detail',
    ];
}
