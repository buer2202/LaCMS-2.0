<?php

use Illuminate\Database\Seeder;

use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->id = 1;  // 不要改
        $user->name = 'admin';
        $user->password = bcrypt('admin');
        $user->save();
    }
}
