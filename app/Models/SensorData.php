<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor';

    protected $fillable = [
        'category',
        'data',
        'deviceId',
    ];

    public function iot_device():BelongsTo{
        return $this->belongsTo(IotDevice::class, 'deviceId', 'id');
    }
}
