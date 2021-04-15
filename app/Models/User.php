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

    // 自定义字段
    protected $appends = ['isEmailTimeout'];

    // 3天未认证，则失效
    private $emailVerifiedTimeout = 60 * 60 * 24 * 3;

    // 邮箱未认证，是否已超出验证时效  getTypeAttribute
    public function getIsEmailTimeoutAttribute () {
        return true;
        // return (time() - $this->ctime) > $emailVerifiedTimeout;
    }

    // 邮箱是否已验证
    public function getIsVerifiedAttribute () {
        return $this->email_verified_at > 0;
    }

}
