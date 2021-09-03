<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'content'
    ];

    protected $casts = [
        'properties' => 'collection',
    ];

    protected $dates = ['created_at'];
}