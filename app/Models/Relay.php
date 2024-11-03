<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Relay extends Model
{
    use HasFactory;
    protected $table = 'relay';
    protected $fillable = [
        'number',
        'status',
        'deviceId',
        'label',
        'sensorId',
        'on_time',
        'off_time',
        'categoryId',
        'automation',
        'automation_on',
        'automation_off'
    ];

    public function iot_device():BelongsTo{
        return $this->belongsTo(IotDevice::class, 'deviceId', 'id');
    }
}
