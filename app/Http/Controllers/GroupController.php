<?php

namespace App\Http\Controllers;

use App\Group;
use App\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return string
     */
    public function index()
    {
        $groups = Group::all();
        $result = array();
        foreach ($groups as $group) {
            array_push($result, [
                'name' => $group->name,
                'type' => $group->type,
                'privacy' => $group->privacy,
                'interaction' => $group->interaction,
                'totalMembers' => $group->members()->count(),
                'totalOwners' => $group->owners()->count()
            ]);
        }
        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // Verify all parameters are there
        $required = ['name', 'type', 'privacy', 'interaction', 'owner'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $input)) {
                return response('Missing input', 400);
            }
        }

        $user = User::where('email', $input['owner'])->first();

        if ($user == null) {
            return response('user does not exist', 404);
        }

        $group = Group::where('name', $input['name'])->first();

        if ($group != null) {
            return response('group exists', 409);
        }

        $group = new Group([
            'name' => $input['name'],
            'type' => $input['type'],
            'privacy' => $input['privacy'],
            'interaction' => $input['interaction']
        ]);

        $group->save();

        $group->addMember($user);
        $group->addOwner($user);

        return response('success', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  string $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        $group = Group::all()->where('name', $name)->first();

        if ($group == null) {
            return response('Not found', 404);
        } else {
            return response()->json([
                'name' => $group->name,
                'type' => $group->type,
                'privacy' => $group->privacy,
                'interaction' => $group->interaction,
                'totalMembers' => $group->members()->count(),
                'totalOwners' => $group->owners()->count()
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string $name
     * @return \Illuminate\Http\Response
     */
    public function destroy($name)
    {
        $group = Group::where('name', $name)->first();

        if (group == null) {
            return response('not found', 404);
        }
    }
}
