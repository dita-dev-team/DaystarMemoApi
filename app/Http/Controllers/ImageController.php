<?php

namespace App\Http\Controllers;

use App\Group;
use App\Memo;
use App\User;
use File;
use Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function getPhoto($resource, $photo, $id)
    {
        if (($resource === 'user') || ($resource === 'group') || ($resource === 'memo')) {

            //Validate Model is valid and return valid response
            list($param, $model) = $this->validateModelisValid($resource, $id);

            if ($model === null) {
                return response()->json("Resource not found", 400);
            }
            //Get File Image name
            $image = $model->img_url;
            //Return Response of Image
            return $this->retrievePhoto($resource, $photo, $image);
        } else {
            //Return Error if Model is not valid
            return response()->json("$resource not found", 400);
        }


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
            list($param, $model) = $this->validateModelisValid($resource, $id);
            //Return Error if Model
            if ($model === null) {
                //Return Response
                return response()->json("$param not found", 400);
            }
            //Create Filename
            $filename = 'DAYSTAR-' . strtoupper($param) . '-FILE' . $model->id . '.jpg';
        } else {
            return response()->json("Resource not found", 400);
        }
        //Store Image Storage folder.
        $this->storeImage($param, $model, $image, $filename);
        //Return Response if Successful
        return response()->json($model, 201);
    }


    /**
     * Stores Images to Storage Folder and Store Filename to Database
     * @param $user
     * @param $image
     */
    private function storeImage($params, $model, $image, $filename)
    {

        //Store Avatar Storage Path
        $this->storeAvatar($params, $image, $filename);
        //Create/Store Thumbnail
        $this->storeThumbnail($params, $image, $filename);
        //Store filename in Data
        $model->img_url = $filename;
        //Save to Database
        $model->save();
        //Return Response
        return response()->json('Images Stored Successfully', 201);
    }

    //Remove A User Image
    public function deletePhoto($resource, $id)
    {
        //Validate Resource is Valid
        if (($resource === 'user') || ($resource === 'group') || ($resource === 'memo')) {

            //Validate Model is valid and return valid response
            list($param, $model) = $this->validateModelisValid($resource, $id);
            //Check if Model is valid
            if ($model === null) {
                //Return Error
                return response()->json("Resource not found", 400);
            }
        }
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

    /**
     * @param $image
     * @param $filename
     */
    private function storeThumbnail($params, $image, $filename)
    {   //Minimise and Resize to Thumbnail
        $img = Image::make($image)->resize(400, 400)->encode('jpg', 50);
        //Initialize the File name
        $path = "$params/avatars/thumbnails/$filename";
        //Store Thumbnail
        $store = Storage::put($path, $img->__toString());
    }

    /**
     * @param $image
     * @param $filename
     */
    private function storeAvatar($params, $image, $filename)
    {
        //Store Avatar
        $path = Storage::putFileAs(
            "$params/avatars", $image, $filename
        );
    }

    /**
     * @param $photo
     * @param $image
     * @return \Illuminate\Http\JsonResponse
     */
    private function retrievePhoto($params, $photo, $image)
    {
        //Check if img_url is null
        if ($image != null) {
            //Get Avatar
            if ($photo === 'avatar') {
                $path = storage_path() . "/app/$params/avatars/" . $image;
                if (file_exists($path)) {

                    return response()->file($path);
                }
            } //Get Thumbnail
            elseif ($photo === 'thumbnail') {
                $path = storage_path() . "/app/$params/avatars/thumbnails/" . $image;
                if (file_exists($path)) {
                    return response()->file($path);
                }
            }
        } else {

            return response()->json('Image Not Found', 400);
        }
    }

    /**
     * @param $resource
     * @param $id
     * @return array
     */
    private function validateModelisValid($resource, $id)
    {
        $models = [
            'User' => User::class,
            'Group' => Group::class,
            'Memo' => Memo::class,
        ];
        $param = ucwords($resource);
        $model = $models[$param]::where('id', $id)->first();
        return array($param, $model);
    }

}
