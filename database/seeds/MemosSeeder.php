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
            'memo_body' => 'Test Memo 1',
            'img_url' => '',
            'user_id'=>  \App\User::find(1)->id,
            'to' => \App\User::find(1)->id,
        ],[
            'memo_body' => 'Test Memo 2',
            'img_url' => '',
            'user_id' => \App\User::find(1)->id,
            'to' => \App\User::find(1)->id
        ]]);
    }
}
