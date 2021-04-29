<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::factory()
            ->times(10)
            ->create([
                'ctime' => time(),
                'mtime' => time()
            ]);

        $extendData = [];
        foreach ($users as $item) {
            array_push($extendData, [
                'uid' => $item->id,
                'level' => 1,
            ]);
        }

        DB::table('user_extends')->insert($extendData);
        DB::table('user_extends')->where('uid', 1)->update(['level' => 10]);

        $user = $users->find(1);

        $user->username = '张三';
        $user->email = '15916965182@163.com';
        $user->actived = 1;
        $user->password = bcrypt('abc12345678');
        $user->save();
    }
}
