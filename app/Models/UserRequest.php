<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends model
{
    use HasFactory;

    protected $table = 'user_request';

    protected $fillable = [
        'roomId',
        'userId',
        'isVerified',
    ];
}
