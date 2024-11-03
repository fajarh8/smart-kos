<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IotDevice extends Model
{
    use HasFactory;
    protected $table = 'iot_device';
    protected $fillable = [
        'name',
        'token',
        'roomId',
    ];
    protected $hidden = [
        'token',
    ];

    public function room():BelongsTo{
        return $this->belongsTo(Room::class, 'roomId', 'id');
    }
    public function sensor():HasMany{
        return $this->hasMany(SensorData::class, 'deviceId', 'id');
    }

    public function relay():HasMany{
        return $this->hasMany(Relay::class, 'deviceId', 'id');
    }
}
