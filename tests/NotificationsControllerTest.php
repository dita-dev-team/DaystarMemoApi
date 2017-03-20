<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationsControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTokenPost()
    {
        $user = factory(App\User::class)->create();
        $this->post('/api/notification/' . $user->id . '/token');
        //$this->assertTrue(true);
        $this->assertResponseOk();
    }
}
