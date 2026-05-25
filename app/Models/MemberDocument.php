<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDocument extends Model
{
    protected $fillable = ['society_id', 'member_id', 'title', 'doc_type', 'file_path', 'notes'];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
