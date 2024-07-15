<?php
namespace App\Service;

use App\Models\ImageB64;
use Storage;

use Intervention\Image\ImageManager;

use DB;
class ImageProcessor implements ImageProcessorInterface
{
    public function convertImage(ImageB64 $imagem)
    {
        // $data = 'data:image/png;base64,AAAFBfj42Pj4';
        dblog('image convert', $imagem->imageable_id);
        if(strpos($imagem->base64, 'data:image') !== 0){
            return;
        }

        $base_path = self::urlTest('storage','storage_tmp','images');
        dblog('image base', $base_path);

        $image = $imagem->base64;
        $type = $imagem->type;
        $item_id = $imagem->imageable_id;
        // data:image/jpeg;base64,
        list($ntype, $image2) = explode(';', $image);
        list(,$extension) = explode('/',$ntype);
        list(,$image2)      = explode(',', $image2);
        $fileName = substr($type,0,4) . "-" . $item_id . '.' . $extension;
        $imageData = base64_decode($image);
        if($imageData===false){
            $imageData = base64_decode($image2);
        }
        $saving = file_put_contents($base_path.DIRECTORY_SEPARATOR.$fileName, $imageData);
        dblog('image path', $base_path.DIRECTORY_SEPARATOR.$fileName);
        dblog('image salvo', print_r($saving, true) );
        $imagem->disk_path = 'images/' . $fileName;
        $imagem->save();


        $images_files = glob($base_path . DIRECTORY_SEPARATOR . $fileName, GLOB_BRACE);
        $manager = new ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );
        foreach ( $images_files as $file) {
            $pieces = explode('/', $file);
            $file_name = end($pieces);

            $lin = ImageB64::where('disk_path', 'images/'.$file_name)->whereNull('base64')->first();
            if($lin){
                continue;
            }

            $type = substr($file_name,0,4);
            $w=$h=200;
            if($type=='prod'){
                $w=$h=720;
            }
            list($width, $height, , ) = getimagesize($file);
            if($width<=$w && $height<=$h){
                $lin = ImageB64::where('disk_path', 'images/'.$file_name)->first();
                $lin->base64 = NULL;
                $lin->save();
                continue;
            }
            $img = $manager->read($file);
            $img->resize($w, $h, function ($const) {
                $const->aspectRatio();
            })->save($file);
            $lin = ImageB64::where('disk_path', 'images/'.$file_name)->first();
            $lin->base64 = NULL;
            $lin->save();
        }

        $sync_script =  'sync_aws_s3.sh';
        $bsync = self::urlTest('scripts', $sync_script);
        $output = shell_exec($bsync . ' -d '.$base_path.' 2>&1');
        dblog('image converter enviando para S3', "...");
    }

/*
    public function convertImage(ImageB64 $imagedb){

        $imagens = ImageB64::whereNull('disk_path')->get();
        foreach ($imagens as $imagem){
            // $data = 'data:image/png;base64,AAAFBfj42Pj4';
            if(strpos($imagem->base64, 'data:image') !== 0){
                continue;
            }
            $image = $imagem->base64;
            $type = $imagem->type;
            $item_id = $imagem->imageable_id;
            list($ntype, $image) = explode(';', $image);
            list(,$extension) = explode('/',$ntype);
            list(,$image)      = explode(',', $image);
            $fileName = substr($type,0,4) . "-" . $item_id . '.' . $extension;
            $imageData = base64_decode($image);
            Storage::disk('images')->put($fileName, $imageData);
            $imagem->disk_path = 'images/' . $fileName;
            $imagem->save();
        }

        $base_path = self::urlTest('storage','storage_tmp','images');
        $images_files = glob($base_path . DIRECTORY_SEPARATOR . '*', GLOB_BRACE);
        $manager = new ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );
        foreach ( $images_files as $file) {
            $pieces = explode('/', $file);
            $file_name = end($pieces);

            $lin = ImageB64::where('disk_path', 'images/'.$file_name)->whereNull('base64')->first();
            if($lin){
                continue;
            }

            $type = substr($file_name,0,4);
            $w=$h=200;
            if($type=='prod'){
                $w=$h=720;
            }
            list($width, $height, , ) = getimagesize($file);
            if($width<=$w && $height<=$h){
                $lin = ImageB64::where('disk_path', 'images/'.$file_name)->first();
                $lin->base64 = NULL;
                $lin->save();
                continue;
            }
            $img = $manager->read($file);
            $img->resize($w, $h, function ($const) {
                $const->aspectRatio();
            })->save($file);
            $lin = ImageB64::where('disk_path', 'images/'.$file_name)->first();
            $lin->base64 = NULL;
            $lin->save();
        }

        $sync_script =  'sync_aws_s3.sh';
        $bsync = self::urlTest('scripts', $sync_script);
        $output = shell_exec($bsync . ' -d '.$base_path.' 2>&1');
        dblog('image converter enviando para S3', "...");
    }
*/
    public static function urlTest(...$segments){
        $BASE = config('thisapp.BASE_OF_LARAVEL');
        if(!empty($BASE)){
            $base_path = $BASE;
        } else {
            $base_path = dirname( dirname( dirname( __FILE__ ) ) );
        }
        array_unshift($segments, $base_path);
        return join(DIRECTORY_SEPARATOR, $segments);
    }
}

