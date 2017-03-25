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

        $this->json('POST', '/api/groups/9999/join', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/9999/leave', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/join', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->id . '/leave', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->id . '/join', [
            'user' => $user->id + 10
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/leave', [
            'user' => $user->id + 10
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/join', [
            'user' => $user->id
        ])->seeStatusCode(200);

        $this->assertEquals(1, $user->groups()->count());

        $this->json('POST', '/api/groups/' . $group->id . '/leave', [
            'user' => $user->id
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

        $this->json('POST', '/api/groups/9999/owner/add', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/9999/owner/remove', [])
            ->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/add', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/remove', [])
            ->seeStatusCode(400);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/add', [
            'user' => $user->id + 10
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/remove', [
            'user' => $user->id + 10
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/add', [
            'user' => $user->id
        ])->seeStatusCode(401);

        $group->addMember($user);

        $this->json('POST', '/api/groups/' . $group->id . '/owner/add', [
            'user' => $user->id
        ])->seeStatusCode(200);

        $this->assertEquals(1, $group->owners()->count());

        $this->json('POST', '/api/groups/' . $group->id . '/owner/remove', [
            'user' => $user->id
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
            'owner' => $user->id + 10
        ])->seeStatusCode(404);

        $this->json('POST', '/api/groups', [
            'name' => $group->name,
            'type' => $group->type,
            'privacy' => $group->privacy,
            'interaction' => $group->interaction,
            'owner' => $user->id
        ])->seeStatusCode(201);

        $result = json_decode($this->response->content());

        $this->json('POST', '/api/groups', [
            'name' => $group->name,
            'type' => $group->type,
            'privacy' => $group->privacy,
            'interaction' => $group->interaction,
            'owner' => $user->id
        ])->seeStatusCode(409);

        $this->get('/api/groups')
            ->seeJson([
                'name' => $group->name,
                'totalOwners' => 1
            ]);

        $this->get('/api/groups/' . $result->id)
            ->seeStatusCode(200)
            ->seeJson([
                'name' => $group->name,
                'totalOwners' => 1
            ]);

        /* $this->get('/api/groups/90');

         $this

         dd($this->response->getContent());*/

    }
}
