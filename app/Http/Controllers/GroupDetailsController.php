<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

class GroupDetailsController extends Controller
{
    public function join($name, Request $request)
    {
        $group = Group::where('name', $name)->first();

        if ($group == null) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

        if (!$request->has('username')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::where('email', $request->input('username'))->first();

        if ($user == null) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

        $group->addMember($user);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function leave($name, Request $request)
    {
        $group = Group::where('name', $name)->first();

        if ($group == null) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

        if (!$request->has('username')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::where('email', $request->input('username'))->first();

        if ($user == null) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

        $group->removeMember($user);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
