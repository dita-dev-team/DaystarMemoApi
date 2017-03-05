<?php

namespace App\Http\Controllers;

use App\Group;
use App\Memo;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class MemoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $required = ['body', 'from', 'to', 'toGroup'];
        foreach ($required as $key) {
            if (!array_key_exists($key, $input)) {
                return response('Missing input', 400);
            }
        }

        if (array_key_exists('isFile', $input) && !array_key_exists('file', $input)) {
            return response('Missing input', 400);
        }

        $sender = User::findOrFail($input['from']);

        $group = null;
        $user = null;
        $filename = null;
        if ($input['toGroup']) {
            $group = Group::findOrFail($input['to']);
        } else {
            $user = User::findOrFail($input['to']);
        }

        $multiple = $input['toGroup'];
        $isFile = array_key_exists('isFile', $input);

        if ($isFile && $input['file']->isValid()) {
            $filename = Uuid::generate() . '.' . $input['file']->getExtension();
            $input['file']->storeAs('memos/' . Carbon::now()->toDateString(), $filename);
        }

        $memo = new Memo(['body' => $input['body']]);
        $memo->setSender($sender);
        $memo->setRecipient($multiple ? $group : $user);
        $memo->is_file = $isFile;
        $memo->filename = $filename;
        $memo->saveOrFail();

        return response($memo->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $memo = Memo::findOrFail($id);
        $response = [
            'id' => $memo->id,
            'body' => $memo->body,
            'sender' => $memo->from->email,
            'date' => $memo->created_at->toDateTimeString(),
        ];

        if ($memo->is_file != 0) {
            $response['filename'] = $memo->filename;
        }

        return response()->json($response);
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
