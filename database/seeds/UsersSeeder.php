<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
class UsersSeeder extends Seeder
{
    public function run()
    {
        // 通过 factory 方法生成 100 个用户并保存到数据库中
       $users=factory(\App\Models\User::class, 100)->create();

       $emailArr=['lilei@qq.com','leilei@qq.com'];

        foreach ($users as $user) {
            if(in_array($user->email,$emailArr)){
                $user->update(['email_verified_at' =>Carbon::now()]);
            }
            
        }
    }
}
