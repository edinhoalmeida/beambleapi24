<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

use Validator;

use App\Models\UserProducts;

use App\Models\User;
use App\Http\Resources\ImageB64 as ImageResource;

class ImageController extends BaseController
{
    public function get(Request $request, $user_id, $type = 'image')
    {
        $image = '';
        if(is_numeric($user_id) && $user = User::find($user_id))
        {
            if($type == 'image') {
                if($image = $user->image()) {
                    $image2 = new ImageResource($image);
                    $image = $image2->base64;

                    $product_path = $image2->disk_path;
                    $AWS_CDN = config('thisapp.AWS_CDN');
                    if(!empty($product_path) && !empty($AWS_CDN)){
                        $product_path = $AWS_CDN. '/'. $product_path;
                        header('Location: '. $product_path);
                        exit;
                    }

                } else {
                    $image = ImageResource::placeholder();
                }
            } else {
                if($logo = $user->logo()) {
                    $image2 = new ImageResource($logo);
                    $image = $image2->base64;
                    $product_path = $image2->disk_path;
                    $AWS_CDN = config('thisapp.AWS_CDN');
                    if(!empty($product_path) && !empty($AWS_CDN)){
                        $product_path = $AWS_CDN. '/'. $product_path;
                        header('Location: '. $product_path);
                        exit;
                    }
                } else {
                    $image = ImageResource::placeholder_logo();
                }
            }
        } else {
            $user_id = 0;
            if($type == 'image') {
                $image = ImageResource::placeholder();
            } else {
                $image = ImageResource::placeholder_logo();
            }
        }
        $dafault = ImageResource::placeholder();
        if(strpos($image,'data:image')!==0){
            $image = $dafault;
        }
        // $data = 'data:image/png;base64,AAAFBfj42Pj4';
        list($ntype, $image) = explode(';', $image);
        list(,$extension) = explode('/',$ntype);
        list(,$image)      = explode(',', $image);
        $fileName = substr($type,0,1) . $user_id . '.' . $extension;
        $imageData = base64_decode($image);
        return response()->streamDownload(function () use ($imageData) {
            echo $imageData;
        }, $fileName);
    }
    public function getl(Request $request, $img_id)
    {
        return $this->get($request, $img_id, 'logo');
    }
    public function getp(Request $request, $product_id)
    {
        if(is_numeric($product_id) && $product = UserProducts::find($product_id))
        {
            if($image = $product->image){
                $image = new ImageResource($image);
                $product_image = $image->base64;
                $product_path = $image->disk_path;
                $AWS_CDN = config('thisapp.AWS_CDN');
                if(!empty($product_path) && !empty($AWS_CDN)){
                    $product_path = $AWS_CDN. '/'. $product_path;
                    header('Location: '. $product_path);
                    exit;
                }
            }
        }
        if(strpos($product_image,'data:image')!==0){
            $product_image = ImageResource::placeholder_product();
        }
        // $data = 'data:image/png;base64,iVBORw';
        list($ntype, $image) = explode(';', $product_image);
        list(,$extension) = explode('/',$ntype);
        list(,$product_image)      = explode(',', $image);
        $fileName = "prod-" . $product_id . '.' . $extension;
        $imageData = base64_decode($product_image);
        return response()->streamDownload(function () use ($imageData) {
            echo $imageData;
        }, $fileName);
    }
}
