<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

class TokenChangedEventTest extends TestCase
{
    use DatabaseTransactions;

    public function testTokenChangedEvent()
    {
        //$this->expectsEvents(\App\Events\TokenChanged::class);
        Event::fake();
        $user = factory(App\User::class)->create();
        $this->post('/api/notification/' . $user->id . '/token');
        Event::assertFired(\App\Events\TokenChanged::class);
    }
}
