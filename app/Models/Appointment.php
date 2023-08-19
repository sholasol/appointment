<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointment extends Model
{
    use HasFactory;

    // protected $guarded = [];

    protected $fillable = [
        'title',
        'client_id',
        'start_time',
        'end_time',
        'description',
        'status'
    ];

    // protected $appends =['formatted_start_time'];

    protected $casts = [
        'start_time' =>'datetime',
        'end_time'   => 'datetime',
    ];


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // public function formattedStartTime(): Attribute
    // {
    //     return Attribute::make(
    //         get : fn () => $this->start_time->format('Y-m-d h:i A'),
    //     );  
    // }
}