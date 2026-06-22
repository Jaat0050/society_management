<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberFamily extends Model
{
    protected $table = 'member_family';

    protected $fillable = ['member_id', 'name', 'relation', 'phone', 'age'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
