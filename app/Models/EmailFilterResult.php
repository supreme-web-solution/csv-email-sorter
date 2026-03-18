<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailFilterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'token',
        'source_count',
        'exclude_count',
        'result_count',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
