<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // 通过 factory 方法生成 100 个用户并保存到数据库中
        factory(\App\Models\User::class, 100)->create();

        $lilei=\App\Models\User::query()->where(['id'=>'1'])->get();
        $lilei->update(['email_verified_at' =>Carbon::now(),'name'=>'lilei','email'=>'lilei@qq.com','password'=>'$10$mJc9G3LblU7jsJvDhr3UbuVwGNZzAPb9b0BJwtmKk9KW38LKUUYLW']);
            
    }
}
