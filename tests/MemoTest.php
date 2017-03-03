<?php


use Illuminate\Foundation\Testing\DatabaseTransactions;

class MemoTest extends TestCase
{
    use DatabaseTransactions;

    public function testMemoCreation()
    {
        $group = factory(App\Group::class)->create();
        $user1 = factory(App\User::class)->create();
        $user2 = factory(App\User::class)->create();
        $memo = factory(App\Memo::class)->make();
        $memo->setSender($user1);
        $memo->setRecipient($user2);
        $memo->save();

        $memos = App\Memo::all();
        $this->assertCount(1, $memos, 'Count should be equal to 1');
        $result = $memos->first();
        $this->assertEquals($memo->body, $result->body, 'Should be equal');
        $this->assertEquals($user1->name, $result->from->name);
        $this->assertEquals($user2->name, $result->to->name);

        $memo->delete();
        $memos = App\Memo::all();
        $this->assertCount(0, $memos, 'Count should be equal to 0');
        $memo = factory(App\Memo::class)->make();
        $memo->setSender($user1);
        $memo->setRecipient($group);
        $memo->save();

        $memos = App\Memo::all();
        $this->assertCount(1, $memos, 'Count should be equal to 1');
        $result = $memos->first();
        $this->assertEquals($group->name, $result->to->name);

    }
}
