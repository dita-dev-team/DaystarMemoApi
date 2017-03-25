<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{

    use DatabaseTransactions;

    /**
     * Test adding and removing of connections to users
     *
     */
    public function testUserConnectionCreation()
    {
        $user1 = factory(App\User::class)->create();
        $user2 = factory(App\User::class)->create();

        $users = App\User::all();

        $this->assertCount(3, $users, 'Count should be equal to 2');

        $user1->addConnection($user2);
        $this->assertEquals(1, $user1->connections()->count());
        $this->assertEquals($user2->id, $user1->connections()->first()->id, "Should be equal");

        $user1->removeConnection($user2);
        $this->assertEquals(0, $user1->connections()->count());

    }

    public function testApiAuthentication()
    {
        $this->json('GET', '/testauth')
            ->seeStatusCode(401);

        $user = factory(App\User::class)->make();

        $this->json('POST', '/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ])->seeStatusCode(200);
        // test duplicates
        $this->json('POST', '/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ])->seeStatusCode(422);

        $client = factory(\Laravel\Passport\Client::class)->make();
        $client->redirect = $this->baseUrl . '/testauth';
        $client->save();

        $this->json('POST', '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $user->email,
            'password' => 'abc',
            'scope' => ''
        ])->seeStatusCode(401);

        $this->json('POST', '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $user->email,
            'password' => $user->password,
            'scope' => ''
        ])->see('access_token');
        $accessToken = $this->decodeResponseJson()['access_token'];

        $this->json('GET', '/testauth', [], [
            'Authorization' => 'Bearer ' . $accessToken
        ])->seeJson([
            'message' => 'This is just a test authentication page'
        ]);

        $new = $user->password . 'newpassword';

        $this->json('POST', '/api/user/change-password', [
            'password' => $new,
            'password_confirmation' => $new,
        ], [
            'Authorization' => 'Bearer ' . $accessToken
        ]);
        $this->assertResponseStatus(422);

        $this->json('POST', '/api/user/change-password', [
            'current_password' => $user->password,
            'password' => $new,
        ], [
            'Authorization' => 'Bearer ' . $accessToken
        ]);
        $this->assertResponseOk();


        $user = \App\User::all()->first();

        $this->assertTrue(password_verify($new, $user->password));

        $user1 = factory(App\User::class)->create();
        $user2 = factory(App\User::class)->create();
        $user3 = factory(App\User::class)->create();
        $group = factory(App\Group::class)->create();
        $user->addConnection($user1);
        $user->addConnection($user2);
        $user->addConnection($user3);
        $group->addOwner($user);
        $group->addMember($user);
        $group->addMember($user1);

        $this->assertEquals(3, $user->connections()->count());
        $this->assertEquals(1, $user->groups()->count());

        $this->json('GET', '/api/user/profile', [], [
            'Authorization' => 'Bearer ' . $accessToken
        ])->seeStatusCode(200);
        $this->seeJsonStructure([
            'connections' => [
                '*' => ['name', 'id']
            ],
            'groups' => [
                '*' => ['name', 'id']
            ]
        ]);
    }
}
