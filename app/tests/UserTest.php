<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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

        $this->assertCount(2, $users, 'Count should be equal to 2');

        $user1->addConnection($user2);
        $this->assertEquals(1, $user1->connections()->count());
        $this->assertEquals($user2->id, $user1->connections()->first()->id, "Should be equal");

        $user1->removeConnection($user2);
        $this->assertEquals(0, $user1->connections()->count());

    }
}
