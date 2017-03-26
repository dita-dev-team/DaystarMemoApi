<?php

use Illuminate\Database\Seeder;

class MemosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('memos')->insert([[
            'content' => 'Test Memo 1',
            'img_url' => '',
            'user_id'=>  \App\User::find(1)->id,
            'to_user' => \App\User::find(1)->id,
        ],[
            'content' => 'Test Memo 2',
            'img_url' => '',
            'user_id' => \App\User::find(1)->id,
            'to_user' => \App\User::find(1)->id
        ]]);
    }
}
