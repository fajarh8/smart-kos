<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KwhHistory extends Model
{
    use HasFactory;
    protected $table = 'kwh_history';
    protected $fillable = [
        'userId',
        'roomId',
        'tariff',
        'day',
        'month',
        'year',
        'hour',
        'kwh',
        'bill',
    ];
}
