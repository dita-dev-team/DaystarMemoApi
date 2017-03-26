<?php

namespace App\Http\Controllers;

use App\Memo;
use App\User;
use App\Group;
use Illuminate\Http\Request;
use Validator;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /*
         * Returns all memos
         */

        $memos = Memo::all();
        $result = array();

        foreach ($memos as $memo) {
            array_push($result, [
                'id' => $memo->id,
                'file_url' => $memo->img_url,
                'memo_body' => $memo->content,
                'user_id' => $memo->user_id,
                'to_user' => $memo->to_user,
                'to_group' => $memo->to_group,
                'time' => $memo->created_at
            ]);
        }
        return response()->json($result, 200);
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
        /*
         * Receives:
         *  User Id as 'user_id',
         *  Memo content as 'memo_body',
         *  Recipient User or Group Id as 'to_user' or 'to_group',
         *  Files as 'file' optional
         *
         */

        $memo = new Memo;
        $user = new User;
        $group = new Group;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'memo_body' => 'required',
            'to_user' => 'sometimes',
            'to_group' => 'sometimes',
            'file' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 403);
        }elseif ($request->get('to_user') === null && $request->get('to_group') === null){
            return response()->json(['The Recipient Must Either be a User or a Group but not both'], 403);
        }elseif (!ctype_digit(strval($request->get('user_id'))) || !ctype_digit(strval($request->get('to_user'))) && !ctype_digit(strval($request->get('to_group')))) {
            return response()->json(['Invalid User Id Type', 'or', 'Invalid Recipient Id', 'Expecting Int Value'], 403);
        }else {

            if (!$user->find($request->get('user_id'))) {
                return response()->json('User Id Not Found', 404);
            }elseif (!$user->find($request->get('to_user')) && !$user->find($request->get('to_group'))) {
                return response()->json('Recipient User or Group Id Not Found', 404);
            }elseif(!$request->get('to_group') && $request->get('to_user')){

                $user_id = $user->find($request->get('user_id'))->id;
                $memo_body = $request->memo_body;
                $to_user = $user->find($request->get('to_user'))->id;

                if ($request->hasFile('file')) {
                    $file = $request->file('file');

                    $filename = time() . '.' .$file->getClientOriginalExtension();
                    $destinationPath = storage_path('memos/');

                    $file->move($destinationPath, $filename);

                    $filePath = $destinationPath . $filename;

                    $memo->user_id = $user_id;
                    $memo->content = $memo_body;
                    $memo->to_user = $to_user;
                    $memo->img_url = $filePath;

                    $memo->save();

                    $input = ['Memo Id' => $memo->id,'Sender User Id' => $user_id, 'Memo Body' => $memo_body, 'Recipient User Id' => $to_user, 'Image_url' => $filePath];

                    return response()->json(['Saved', $input], 200);
                }else {
                    $memo->user_id = $user_id;
                    $memo->content = $memo_body;
                    $memo->to_user = $to_user;

                    $memo->save();

                    $input = ['Memo Id' => $memo->id, 'Sender User Id' => $user_id, 'Memo Body' => $memo_body, 'Recipient User Id' => $to_user];

                    return response()->json(['Saved', $input], 200);
                }
            }elseif($request->get('to_group') && !$request->get('to_user')){
                $user_id = $user->find($request->get('user_id'))->id;
                $memo_body = $request->memo_body;
                $to_group = $group->find($request->get('to_group'))->id;

                if ($request->hasFile('file')) {
                    $file = $request->file('file');

                    $filename = time() . '.' .$file->getClientOriginalExtension();
                    $destinationPath = storage_path('memos/');

                    $file->move($destinationPath, $filename);

                    $filePath = $destinationPath . $filename;

                    $memo->user_id = $user_id;
                    $memo->content = $memo_body;
                    $memo->to_group = $to_group;
                    $memo->img_url = $filePath;

                    $memo->save();

                    $input = ['Memo Id' => $memo->id,'Sender User Id' => $user_id, 'Memo Body' => $memo_body, 'Recipient Group Id' => $to_group, 'Image_url' => $filePath];

                    return response()->json(['Saved', $input], 200);
                }else {
                    $memo->user_id = $user_id;
                    $memo->content = $memo_body;
                    $memo->to_group = $to_group;

                    $memo->save();

                    $input = ['Memo Id' => $memo->id, 'Sender User Id' => $user_id, 'Memo Body' => $memo_body, 'Recipient Group Id' => $to_group];

                    return response()->json(['Saved', $input], 200);
                }

            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Memo $memo
     * @return \Illuminate\Http\Response
     */
    public function show(Memo $memo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Memo $memo
     * @return \Illuminate\Http\Response
     */
    public function edit(Memo $memo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Memo $memo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Memo $memo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Memo $memo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Memo $memo)
    {
        //
    }
}
