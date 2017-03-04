<?php

namespace App\Http\Controllers;

use App\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class AssetController extends Controller
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
        //print_r($input);
        if ($request->hasFile('asset') && $request->file('asset')->isValid()) {
            $filename = Uuid::generate() . '.' . $request->asset->getExtension();
            $path = $request->asset->storeAs('assets', $filename);

            if ($path != null) {
                $description = $request->description;
                $type = $request->asset->extension();
                $size = $request->asset->getSize();

                $asset = new Asset([
                    'description' => $description,
                    'type' => $type,
                    'size' => $size,
                    'name' => $filename
                ]);
                $asset->save();
                return response($asset->id, 200);
            }

            return response('an error occurred while saving the file', 500);
        }

        return response('Invalid upload', 400);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $asset = Asset::find($id);

        if ($asset == null) {
            return response('Not found', 404);
        }

        //$file = Storage::get($asset->name);

        //return (new Response($file, 200))->header('Content-Type', $asset->type);
        return response()->download(storage_path('app/assets/') . $asset->name, $asset->description);
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
        $asset = Asset::find($id);

        if ($asset == null) {
            return response('Not found', 404);
        }
        Storage::delete($asset->filepath);

        $asset->delete();

        return response('success', 200);
    }
}
