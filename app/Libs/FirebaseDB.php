<?php 
namespace App\Libs;

use Kreait\Firebase\Factory;

use App\Exceptions\FirebaseException;

class FirebaseDB {

    private $factory = null;
    private $database  = null;
    private $tablepins  = null;
    private $tablevideocalls  = null;
    private $tableaskcalls  = null;
    private static $instance = null;

    public function __construct()
    {
        $this->tablepins = env('FIREBASE_REALTIME_PINS');
        $this->tablevideocalls = env('FIREBASE_REALTIME_VIDEOCALLS');
        $this->tableaskcalls = env('FIREBASE_REALTIME_ASKCALLS');
        $file_settings = env('FIREBASE_CREDENTIALS');
        $database_uri = env('FIREBASE_REALTIME_DB');
        $file_set = base_path($file_settings);
        if(file_exists($file_set)){
            $this->factory = (new Factory)
                ->withServiceAccount($file_set)
                ->withDatabaseUri($database_uri);
            $this->database = $this->factory->createDatabase();
        } else {
            throw new FirebaseException('Firebase credentials not found.');
        }
    }

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new FirebaseDB();
        }
        return self::$instance;
    }

    // update or insert
    public function pin_update($data)
    {
        if(($data = $this->_validate($data)) === false){
            return null;
        }
        $reference = $this->database->getReference($this->tablepins);
        $exists = $reference
            ->orderByChild('user_id')
            ->equalTo($data['user_id'])
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            $saved_data = current($exists);
            $differs = array_diff($saved_data, $data);
            if(empty($differs)) {
                return null;
            }
            $this->database->getReference($this->tablepins . $fbdb_key)->set($data);
        } else {
            $reference->push($data);
        }
    }

    // update or insert
    public function pin_delete($user_id)
    {
        $reference = $this->database->getReference($this->tablepins);
        $exists = $reference
            ->orderByChild('user_id')
            ->equalTo($user_id)
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            $this->database->getReference($this->tablepins . $fbdb_key)->remove();
        }
    }

    private function _validate($data){
        if(!is_object($data)){
            $data = (array) $data; 
        }
        if(!array_key_exists('user_id', $data)){
           return false;
        }
        if(!array_key_exists('lat', $data)){
            return false;
        }
        if(!array_key_exists('lng', $data)){
            return false;
        }
        if(!array_key_exists('company_type', $data)){
            return false;
        }
        return [
            'user_id'=>(int)$data['user_id'], 
            'lat'=>(float)$data['lat'], 
            'lng'=>(float)$data['lng'],
            'company_type'=>$data['company_type'],
        ];
    }

    public function test(){
        $this->pin_update(['user_id'=>2,'lat'=>rand(100000, 900000)/10000,'lng'=>rand(100000, 900000)/10000]);
        $this->pin_update(['user_id'=>5,'lat'=>rand(100000, 900000)/10000,'lng'=>rand(100000, 900000)/10000]);
        $this->pin_update(['user_id'=>7,'lat'=>rand(100000, 900000)/10000,'lng'=>rand(100000, 900000)/10000]);
    }
    public function test2(){
        $this->pin_delete(5);
    }

    // update or insert
    public function videocall_update($data)
    {
        $reference = $this->database->getReference($this->tablevideocalls);
        $exists = $reference
            ->orderByChild('videocall_id')
            ->equalTo($data['videocall_id'])
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            // $saved_data = current($exists);
            // dd($saved_data);
            // $differs = array_diff($saved_data, $data);
            // if(empty($differs)) {
            //     return null;
            // }
            $this->database->getReference($this->tablevideocalls . $fbdb_key)->set($data);
        } else {
            $reference->push($data);
        }
    }

    // delete
    public function videocall_delete($videocall_id)
    {
        $reference = $this->database->getReference($this->tablevideocalls);
        $exists = $reference
            ->orderByChild('videocall_id')
            ->equalTo($videocall_id)
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            $this->database->getReference($this->tablevideocalls . $fbdb_key)->remove();
        }
    }

    // update or insert
    public function askcall_update($data)
    {
        $reference = $this->database->getReference($this->tableaskcalls);
        $exists = $reference
            ->orderByChild('user_id')
            ->equalTo($data['user_id'])
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            $this->database->getReference($this->tableaskcalls . $fbdb_key)->set($data);
        } else {
            $reference->push($data);
        }
    }

    // delete
    public function askcall_delete($user_id)
    {
        $reference = $this->database->getReference($this->tableaskcalls);
        $exists = $reference
            ->orderByChild('user_id')
            ->equalTo($user_id)
            ->getSnapshot()->getValue();
        if(count($exists)){
            $fbdb_key = key($exists);
            $this->database->getReference($this->tableaskcalls . $fbdb_key)->remove();
        }
    }

}