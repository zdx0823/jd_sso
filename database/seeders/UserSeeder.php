<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

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

        $user = $users->find(1);

        $user->username = '张三';
        $user->email = '15916965182@163.com';
        $user->actived = 1;
        $user->password = bcrypt('abc12345678');
        $user->save();
    }
}
