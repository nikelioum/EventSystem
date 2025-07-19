<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'event_date',
        'location',
        'capacity',
        'current_registrations_count',
        'status',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'status' => 'string',
    ];


    

    public function users()
    {
        return $this->belongsToMany(User::class, 'event_registrations')
                    ->withTimestamps()
                    ->withPivot('created_at', 'updated_at');
    }

    public function isFull()
    {
        return $this->current_registrations_count >= $this->capacity;
    }
}
