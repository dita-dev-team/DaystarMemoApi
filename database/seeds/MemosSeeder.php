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
            'file_url' => '',
            'user_id'=>  \App\User::find(5)->id,
            'to' => \App\User::find(5)->id,
        ],[
            'content' => 'Test Memo 2',
            'file_url' => 'img/this.jpg',
            'user_id' => \App\User::find(5)->id,
            'to' => \App\User::find(5)->id
        ]]);
    }
}
