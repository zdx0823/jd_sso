<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Schema::defaultStringLength(191);

        // 自定义验证规则
        Validator::extend('$password', function ($attribute, $value, $parameters, $validator) {

            $result = (mb_strlen($value) < 8 || mb_strlen($value) > 32);
            if ($result) return false;
          
            $result = preg_match('/^[a-zA-Z]/', $value, $p1);
            if (count($p1) === 0) return false;
          
            $arr = str_split($value);
            $result = count(\array_unique($arr)) === 1;
            if ($result) return false;
      
            return true;
        });

    }
}
