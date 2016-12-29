<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GroupTest extends TestCase
{
    use DatabaseTransactions;


    public function testGroupCreation()
    {
        $group = factory(App\Group::class)->create();

        $groups = App\Group::all();

        $this->assertCount(1, $groups, 'Count should be equal to 1');
        $this->assertEquals($group->name, $groups->first()->name);
    }

    public function testMemberCreation()
    {
        $group = factory(App\Group::class)->create();
        $user = factory(App\User::class)->create();

        $group->addMember($user);
        $this->assertEquals(1, $group->members()->count());
        $this->assertEquals($user->id, $group->members()->first()->id);
        $this->assertTrue($group->isMember($user));
        $this->assertEquals(1, $user->groups()->count());
        $this->assertEquals($group->name, $user->groups()->first()->name);

        $group->removeMember($user);
        $this->assertEquals(0, $group->members()->count());
        $this->assertFalse($group->isMember($user));
    }

    public function testOwnerCreation()
    {
        $group = factory(App\Group::class)->create();
        $user = factory(App\User::class)->create();

        $group->addOwner($user);
        $this->assertEquals(1, $group->owners()->count());
        $this->assertEquals($user->id, $group->owners()->first()->id);
        $this->assertTrue($group->isOwner($user));

        $group->removeOwner($user);
        $this->assertEquals(0, $group->owners()->count());
        $this->assertFalse($group->isOwner($user));
    }
}
