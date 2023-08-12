<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    protected $casts = [
        'start_time' =>'datetime',
        'end_time'   => 'datetime',
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}