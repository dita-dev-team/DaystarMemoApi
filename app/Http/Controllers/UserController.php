<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20/03/17
 * Time: 18:40
 */

namespace App\Http\Controllers;


use App\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $result = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'connections' => [],
            'groups' => []
        ];

        /** @var User $connection */
        foreach ($user->connections as $connection) {
            array_push($result['connections'], [
                'id' => $connection->id,
                'name' => $connection->name
            ]);
        }

        /** @var Group $group */
        foreach ($user->groups as $group) {
            array_push($result['groups'], [
                'id' => $group->id,
                'name' => $group->name
            ]);
        }

        return response()->json($result);

    }


    public function change(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $request->user();
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return response()->json(['status' => 'success']);
    }

    public function validator(array $data)
    {
        $messages = [
            'current_password.required' => 'please enter current password',
            'password.required' => 'please enter password'
        ];

        return Validator::make($data, [
            'current_password' => 'required',
            'password' => 'required|same:password',
        ], $messages);
    }
}