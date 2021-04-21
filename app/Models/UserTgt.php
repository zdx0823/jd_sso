<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTgt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'tgt',
        'tgc',
        'session_id',
    ];

}
