<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kos extends Model
{
    use HasFactory;
    protected $table = 'kos';
    protected $fillable = [
        'name',
        'address',
        'adminId',
    ];

    public function room():HasMany{
        return $this->hasMany(Room::class, 'kosId', 'id');
    }

    public function user():BelongsTo{
        return $this->belongsTo(User::class, 'adminId', 'id');
    }
}
