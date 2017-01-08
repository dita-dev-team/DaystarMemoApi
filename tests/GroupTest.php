<?php

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

    public function testGroupDeletion()
    {
        $group = factory(App\Group::class)->create();
        $user = factory(App\User::class)->create();

        $group->addMember($user);
        $group->delete();
        $this->assertEquals(0, $user->groups()->count());
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

        $user = factory(App\User::class)->create();
        $group = factory(App\Group::class)->create();

        $this->json('POST', '/api/groups/asfs/join', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/asfs/leave', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/join', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->name . '/leave', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->name . '/join', [
            'username' => 'sdfsd'
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/leave', [
            'username' => 'sdfsd'
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/join', [
            'username' => $user->email
        ])->seeStatusCode(200);

        $this->assertEquals(1, $user->groups()->count());

        $this->json('POST', '/api/groups/' . $group->name . '/leave', [
            'username' => $user->email
        ])->seeStatusCode(200);

        $this->assertEquals(0, $user->groups()->count());
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

        $this->json('POST', '/api/groups/asfs/owner/add', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/asfs/owner/remove', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/add', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/remove', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/add', [
            'username' => 'sdfsd'
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/remove', [
            'username' => 'sdfsd'
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/add', [
            'username' => $user->email
        ])->seeStatusCode(401);

        $group->addMember($user);

        $this->json('POST', '/api/groups/' . $group->name . '/owner/add', [
            'username' => $user->email
        ])->seeStatusCode(200);

        $this->assertEquals(1, $group->owners()->count());

        $this->json('POST', '/api/groups/' . $group->name . '/owner/remove', [
            'username' => $user->email
        ])->seeStatusCode(200);

        $this->assertEquals(0, $group->owners()->count());
    }

    public function testGroupRoute()
    {
        $user = factory(App\User::class)->create();
        $group = factory(App\Group::class)->make();

        $this->json('POST', '/api/groups', [
            'name' => $group->name
        ])->seeStatusCode(400);

        $this->json('POST', '/api/groups', [
            'name' => $group->name,
            'type' => $group->type,
            'privacy' => $group->privacy,
            'interaction' => $group->interaction,
            'owner' => 'testuser'
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups', [
            'name' => $group->name,
            'type' => $group->type,
            'privacy' => $group->privacy,
            'interaction' => $group->interaction,
            'owner' => $user->email
        ])->seeStatusCode(201);

        $this->json('POST', '/api/groups', [
            'name' => $group->name,
            'type' => $group->type,
            'privacy' => $group->privacy,
            'interaction' => $group->interaction,
            'owner' => $user->email
        ])->seeStatusCode(409);

        $this->get('/api/groups')
            ->seeJson([
                'name' => $group->name,
                'totalOwners' => 1
            ]);

        $this->get('/api/groups/' . $group->name)
            ->seeJson([
                'name' => $group->name,
                'totalOwners' => 1
            ]);

    }
}
