<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatLogs extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender','recipient','latest_message','unread_count'
    ];
}
