<?php

namespace App\Listeners;

use App\Events\TokenChanged;

class TokenChangedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TokenChanged $event
     * @return void
     */
    public function handle(TokenChanged $event)
    {
        //return response('success', 200);
        echo 'ghjkhjgluiioitfiytouyoiyoiyiuyiuyiuyoiufhgc.m.';
    }
}
