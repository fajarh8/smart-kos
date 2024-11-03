<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBill extends Model
{
    use HasFactory;
    protected $table = 'room_setting';
    protected $fillable = [
        'userId',
        'roomId',
        'electric_access',
		'pirOnHour', 
		'pirOnMin',
		'pirOffHour',
		'pirOffMin',
		'pirInterval',
		'pirSchedule',
    ];
    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'userId', 'id');
    }
    public function room():BelongsTo{
        return $this->belongsTo(Room::class, 'roomId', 'id');
    }
}
