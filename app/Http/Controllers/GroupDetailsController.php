<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

class GroupDetailsController extends Controller
{
    public function join($id, Request $request)
    {
        $group = Group::findOrFail($id);

        if (!$request->has('user')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::findOrFail($request->input('user'));

        $group->addMember($user);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function leave($id, Request $request)
    {
        $group = Group::findOrFail($id);

        if (!$request->has('user')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::findOrFail($request->input('user'));

        $group->removeMember($user);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function addOwner($id, Request $request)
    {
        $group = Group::findOrFail($id);

        if (!$request->has('user')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::findOrFail($request->input('user'));

        $isMember = $user->groups()->where('name', $group->name)->first() != null;

        if (!$isMember) {
            return response()->json([
                'error' => 'unauthorized'
            ], 401);
        }

        $group->addOwner($user);

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function removeOwner($id, Request $request)
    {
        $group = Group::findOrFail($id);

        if (!$request->has('user')) {
            return response()->json([
                'error' => 'missing parameter'
            ], 400);
        }

        $user = User::findOrFail($request->input('user'));

        $group->removeOwner($user);

        return response()->json([
            'status' => 'success'
        ]);
    }
}
