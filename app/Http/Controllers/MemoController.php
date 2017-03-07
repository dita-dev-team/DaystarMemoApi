<?php

namespace App\Http\Controllers;

use App\Memo;
use App\Group;
use App\User;
use Illuminate\Http\Request;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $memos = Memo::all();
        $result = array();

        foreach ($memos as $memo) {
            array_push($result, [
                'id' => $memo->id,
                'file_url' => $memo->file_url,
                'content' => $memo->content,
                'user_id' => $memo->user_id,
                'to' => $memo->to,
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

        $memo = new Memo;
        $user = new User;

        if ($request->get('user_id') == null || $request->get('body_content') == null || $request->get('to') == null) {
            return response()->json('parameters missing', 403);
        } elseif (!ctype_digit(strval($request->get('user_id'))) || !ctype_digit(strval($request->get('to')))) {
            return response()->json(['Invalid User Id Type', 'or', 'Invalid Recipient Id', 'Expecting Int Value'], 403);
        } else {

            if (!$user->find($request->get('user_id'))) {
                return response()->json('User Id Not Found', 400);
            } elseif (!$user->find($request->get('to'))) {
                return response()->json('Recipient User Id Not Found', 400);
            }

            $user_id = $user->find($request->get('user_id'))->id;
            $content = $request->body_content;
            $to = $user->find($request->get('to'))->id;

//         TODO get files and save them. Get uri and save in database
            if ($request->hasFile('file_url')) {
                $file = $request->file('file_url');

                if ($file->getType() /* get file type image*/) {
                    /*Save the image file to disk and get url*/
                } elseif ($file->getType() /*get other file type */) {
                    /*Save the file to disk and get url*/
                }
            }


            $memo->user_id = $user_id;
            $memo->content = $content;
            $memo->to = $to;
//         TODO add file_url to database whenever there is a file included in the memo

            $memo->save();
            return response()->json('Saved', 200);
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
