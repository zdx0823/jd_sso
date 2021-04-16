<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use SoftDeletes;

    const CREATED_AT = 'ctime';
    const UPDATED_AT = 'mtime';
    const DELETED_AT = 'dtime';

    protected $dateFormat = 'U';

    protected $fillable = [
        'username',
        'email',
        'password',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
    ];

    // 访问器
    protected $casts = [
        'ctime' => 'timestamp',
        'mtime' => 'timestamp',
        'dtime' => 'timestamp',
    ];

    // 自定义字段
    // protected $appends = ['isActived'];

    // public function getIsActivedAttribute () {
    //     return $this->email_verified_at > 0;
    // }
}
