<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;
    protected $table = 'room';
    protected $fillable = [
        'name',
        'tariff',
        'kosId',
        'electric_access',
        'power',
        'timezone',
        'userId'
    ];

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'userId', 'id');
    }

    public function kos():BelongsTo{
        return $this->belongsTo(Kos::class, 'kosId', 'id');
    }

    public function iot_device():HasOne{
        return $this->hasOne(IotDevice::class, 'roomId', 'id');
    }
    public function roomBill():HasOne{
        return $this->hasOne(RoomBill::class, 'roomId', 'id');
    }
}
