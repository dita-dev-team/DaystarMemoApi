<?php

namespace App\Http\Controllers;

use App\Memo;
use App\User;
use App\Utilities\ImageUtilities;
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
                'file_url' => $memo->file_url,
                'content' => $memo->memo_body,
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
        /*
         * Receives:
         *  User Id as 'user_id',
         *  Memo content as 'memo_body',
         *  Recipient User Id as 'to',
         *  Files as 'file'
         */

        $memo = new Memo;
        $user = new User;

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'memo_body' => 'required',
            'to' => 'required',
            'file' => 'sometimes'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        } elseif (!ctype_digit(strval($request->get('user_id'))) || !ctype_digit(strval($request->get('to')))) {
            return response()->json(['Invalid User Id Type', 'or', 'Invalid Recipient Id', 'Expecting Int Value'], 400);
        } else {

            if (!$user->find($request->get('user_id'))) {
                return response()->json('User Id Not Found', 400);
            } elseif (!$user->find($request->get('to'))) {
                return response()->json('Recipient User Id Not Found', 400);
            }

            $user_id = $user->find($request->get('user_id'))->id;
            $memo_body = $request->memo_body;
            $to = $user->find($request->get('to'))->id;
            $memo->user_id = $user_id;
            $memo->content = $memo_body;
            $memo->to = $to;

            $memo->save();

            $input = ['Memo Id' => $memo->id, 'Sender User Id' => $user_id, 'Memo Body' => $memo_body, 'Recipient Id' => $to];

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $filename = 'DAYSTAR-MEMO-FILE' . $memo->id . $file->getClientOriginalExtension();
                ImageUtilities::storeImage('memo', $memo, $file, $filename);
            }

            return response()->json(['Saved', $input], 200);
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
