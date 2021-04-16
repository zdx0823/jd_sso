<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegiestToken extends Model
{
    use HasFactory;

    const CREATED_AT = 'ctime';

    protected $dateFormat = 'U';
    
    // 访问器
    protected $casts = [
        'ctime' => 'timestamp',
    ];

}
