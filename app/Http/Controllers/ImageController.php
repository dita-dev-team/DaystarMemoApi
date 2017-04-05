<?php

namespace App\Http\Controllers;

use App\Group;
use App\Memo;
use App\User;
use App\Utilities\ImageUtilities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function getPhoto($resource, $photo, $id)
    {
        if (($resource === 'user') || ($resource === 'group') || ($resource === 'memo')) {

            //Validate Model is valid and return valid response
            list($param, $model) = $this->validateModelIsValid($resource, $id);
            //Get File Image name
            $image = $model->img_url;
            $result = ImageUtilities::getImage($resource, $photo, $image);

            if ($result != null) {
                return response()->file($result);
            }

            return response()->json("not found", 404);
        } else {
            //Return Error if Model is not valid
            return response()->json("$resource not found", 404);
        }
    }

    /**
     * @param $resource
     * @param $id
     * @return array
     */
    private function validateModelIsValid($resource, $id)
    {
        $models = [
            'User' => User::class,
            'Group' => Group::class,
            'Memo' => Memo::class,
        ];
        $param = ucwords($resource);
        $model = $models[$param]::findOrFail($id);
        return array($param, $model);
    }

    /**
     * Stores Photo in the Storage Folder
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function storePhoto(Request $request)
    {
        //Validate requests
        $this->validate($request, [
            'resource' => 'required',
            'image' => 'required',
            'id' => 'required',

        ]);

        //Initialize  filename
        $filename = '';
        //Retrieve Resource
        $resource = strtolower($request['resource']);
        //Retrieve Id.
        $id = $request['id'];
        //Retrieve Image
        $image = $request->file('image');

        //Validate Resource
        if (($resource === 'user') || ($resource === 'group') || ($resource === 'memo')) {
            //Make Model Call Dynamic.
            list($param, $model) = $this->validateModelIsValid($resource, $id);
            //Create Filename
            $filename = 'DAYSTAR-' . strtoupper($param) . '-FILE' . $model->id . '.' . $image->getClientOriginalExtension();
        } else {
            return response()->json("Resource not found", 404);
        }
        //Store Image Storage folder.
        ImageUtilities::storeImage(strtolower($param), $model, $image, $filename);
        //Return Response if Successful
        return response()->json($model, 201);
    }

    public function deletePhoto($resource, $id)
    {
        //Validate Resource is Valid
        if (($resource === 'user') || ($resource === 'group') || ($resource === 'memo')) {

            //Validate Model is valid and return valid response
            list($param, $model) = $this->validateModelIsValid($resource, $id);
            //Get Image Name
            $image = $model->img_url;

            //Delete Avatar and Thumbnail
            if ($image != null) {
                Storage::delete("/$param/avatars/thumbnails/$image");
                Storage::delete("/$param/avatars/$image");
            }
            //Update Profile Photo Field To Null
            $model->img_url = null;
            //Save in Database
            $model->save();
            //Return Response
            return response()->json($model, 202);
        }

        return response('Invalid request', 400);

    }

}
