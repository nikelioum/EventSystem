<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventRegistration extends Model
{
    use HasFactory;

    protected $table = 'event_registrations';

    protected $fillable = [
        'user_id',
        'event_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    
}
