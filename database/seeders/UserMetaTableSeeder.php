<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UserMetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=1; $i < 202; $i++) {
            $url  = "https://randomuser.me/api/portraits/men/$i.jpg";
            if($i > 99){
                $picCount = $i - 100;
                $url  = "https://randomuser.me/api/portraits/women/$picCount.jpg";
            }
            DB::table('user_meta')->insert([
                'user_id' => $i,
                'gender' => $i % 4 === 0 ? 'male' : 'female',
                'cover' => 'https://picsum.photos/1000/1000',
                'display_picture' =>  $url,
                'birthday' => '1996-07-14',
            ]);
        }
    }
}
