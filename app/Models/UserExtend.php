<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExtend extends Model
{
    use HasFactory;

    // 管理员等级
    private const ADMIN_LEVEL = 10;

    public $timestamps = false;

    // 自定义字段
    protected $appends = ['isAdmin'];

    // 访问器
    public function getIsAdminAttribute () {

        return $this->level === self::ADMIN_LEVEL;
    }

    
    public function user () {
        return $this->belongsTo('App\Models\User', 'uid');
    }
}
