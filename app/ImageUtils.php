<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 25/03/17
 * Time: 14:55
 */

namespace App;


use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageUtils
{
    public static function storeImage($params, $model, $image, $filename)
    {
        //Store Avatar Storage Path
        ImageUtils::storeAvatar($params, $image, $filename);
        //Create/Store Thumbnail
        ImageUtils::storeThumbnail($params, $image, $filename);
        //Store filename in Data
        $model->img_url = $filename;
        //Save to Database
        $model->save();
    }

    private static function storeAvatar($params, $image, $filename)
    {
        //Store Avatar
        $path = Storage::putFileAs(
            "$params/avatars", $image, $filename
        );
    }

    private static function storeThumbnail($params, $image, $filename)
    {   //Minimise and Resize to Thumbnail
        $img = Image::make($image)->resize(400, 400)->encode('jpg', 50);
        //Initialize the File name
        $path = "$params/avatars/thumbnails/$filename";
        //Store Thumbnail
        $store = Storage::put($path, $img);
        //dd($path);
    }

    public static function getImage($params, $photo, $image)
    {
        $result = null;
        if ($image != null) {
            if ($photo === 'avatar') {
                $path = storage_path() . "/app/$params/avatars/" . $image;
                $result = file_exists($path) ? $path : null;
            } elseif ($photo === 'thumbnail') {
                $path = storage_path() . "/app/$params/avatars/thumbnails/" . $image;
                $result = file_exists($path) ? $path : null;
            }
        }

        return $result;
    }
}