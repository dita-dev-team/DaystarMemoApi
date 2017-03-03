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
            'file_url' => ''
        ],[
            'content' => 'Test Memo 2',
            'file_url' => 'img/this.jpg'
        ]]);
    }
}
