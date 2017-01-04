<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

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

        $this->assertCount(2, $users, 'Count should be equal to 2');

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

        /*
        $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => $user->password
        ])->seeStatusCode(200);
        */

        $client = factory(\Laravel\Passport\Client::class)->make();
        $client->redirect = $this->baseUrl . '/testauth';
        $client->save();

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
        //dd($accessToken);
        //dd($this->baseUrl);

    }
}
