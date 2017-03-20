<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 20/03/17
 * Time: 13:30
 */

namespace app\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public function change(Request $request)
    {
        $this->validator($request->all())->validate();
        $user = $request->user();
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return response('success');
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
            'password_confirmation' => 'required|same:password'
        ], $messages);
    }

}