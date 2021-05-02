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
    protected $appends = ['isAdmin', 'type'];

    // 访问器
    public function getIsAdminAttribute () {

        return $this->level === self::ADMIN_LEVEL;
    }

    public function getTypeAttribute () {

        switch ($this->level) {
            case 1:
                $res = 'normal';
                break;
            case 3:
                $res = 'seller';
                break;
            case 10:
                $res = 'admin';
                break;
            
            default:
                $res = 'disable';
                break;
        }

        return $res;
    }
    
    public function user () {
        return $this->belongsTo('App\Models\User', 'uid');
    }
}
