<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginSt extends Model
{
    use HasFactory;

    protected $table = 'login_st';
    
    public $timestamps = false;

    protected $fillable = [
        'st',
        'ctime',
    ];
}
