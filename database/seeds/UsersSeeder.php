<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // 通过 factory 方法生成 100 个用户并保存到数据库中$2y$10$nLswIh3CmeO31cbzlIYvb.NeMHQhNgnWkGioCtdNPtIU2vROn906G
        factory(\App\Models\User::class, 100)->create();

        $lilei=\App\Models\User::query()->where(['id'=>'1'])->get();
        $lilei->update(['email_verified_at' =>Carbon::now(),'name'=>'lilei','email'=>'lilei@qq.com','password'=>'$2y$10$nLswIh3CmeO31cbzlIYvb.NeMHQhNgnWkGioCtdNPtIU2vROn906G']);
            
    }
}
