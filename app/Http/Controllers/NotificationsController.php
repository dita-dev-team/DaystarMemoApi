<?php

namespace App\Http\Controllers;

use App\Events\TokenChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;

class NotificationsController extends Controller
{
    public function __invoke($id, Request $request)
    {
        /*$user = User::findOrFail($id);
        $user->device_token = $request->input('deviceToken');
        $user->save();*/
        Event::fire(new TokenChanged());
        //return response('success');
    }
}
