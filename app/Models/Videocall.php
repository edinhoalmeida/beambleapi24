<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;

use App\Models\UserTrack;

use App\Libs\Emissionco2;

use Carbon\Carbon;

class Videocall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id','client_lat','client_lng','distance_between_km','kg_co2', 'beamer_id', 'meeting_id', 'meeting_object',
        'status', 'beamer_agree_at',
        'with_donation','is_freemium',
        'timer_start_at','timer_end_at'
    ];

    protected $hidden = [
        'created_at'
    ];

    protected $casts = [
        'timer_start_at' => 'timestamp',
        'timer_end_at' => 'timestamp',
    ];

    public static function clearExpiredCalls(){
        
        $imutable = Carbon::now();
        
        $time_max_seconds = 180;

        $mutable = Carbon::now();
        $mutable->subSeconds($time_max_seconds);
        $chamadas_perdidas = 
            Videocall::where('created_at', '<', $mutable)->where('status', 'waiting')->get();
        foreach($chamadas_perdidas as $chamada){
            $chamada->status = 'lost';
            $chamada->deleted_at = $imutable;
            $chamada->save();
        }

        $time_min_days = 3;

        $mutable1 = Carbon::now();
        $mutable1->subDays($time_min_days);
        $starts_perdidos = 
            UserTrack::where('updated_at', '<', $mutable1)
            ->where('status', 'on')
            ->get();
        foreach($starts_perdidos as $track){
            $track->status = 'lost';
            $track->deleted_at = $imutable;
            $track->save();
        }
    }

    public static function call_CO2_emissions($call_id){
        
        $Videocall = Videocall::where('id', $call_id)->withTrashed()->first();

        $UserTrack = UserTrack::where('user_id', '=', $Videocall->beamer_id)
            ->where('status', 'on')
            ->withTrashed()->first();


        if( someone_is_empty($Videocall->client_lat, $Videocall->client_lng, $UserTrack->lat, $UserTrack->lng) ) {
           // arbitrary
            $distance_between_km = 150;            
        } else {
            $distance_between_km = Emissionco2::distance($Videocall->client_lat, $Videocall->client_lng, $UserTrack->lat, $UserTrack->lng);
        }

        $kg_co2 = Emissionco2::km_to_emision_kg($distance_between_km);

        dblog('call_CO2_emissions - ' . $call_id . '- distance: ' . $distance_between_km . ', Videocall: ', json_encode($Videocall));

        $Videocall->distance_between_km = ceil($distance_between_km);
        $Videocall->kg_co2 = $kg_co2;
        $Videocall->save();
        
    }


    public static function stats(){

        $calls = DB::select("SELECT YEAR(created_at) as year, MONTH(created_at) as month, WEEK(created_at) as week, is_freemium  
            FROM videocalls 
            WHERE meeting_id IS NOT NULL 


            ORDER BY created_at");

        $lines = ['freemium'=>[],'notfreemium'=>[]];
        foreach($calls as $ca){
            if($ca->week<10){
                $ca->week = "0".$ca->week;
            }
            if($ca->month<10){
                $ca->month = "0".$ca->month;
            }
            $chave = $ca->year .'-'.$ca->month.':'.$ca->week;
            if($ca->is_freemium==1){
                if(isset($lines['freemium'][$chave])){
                    $lines['freemium'][$chave]++;
                }else{
                    $lines['freemium'][$chave] = 1;
                }
                if(!isset($lines['notfreemium'][$chave])){
                    $lines['notfreemium'][$chave] = 0;
                }
            } else{
                if(isset($lines['notfreemium'][$chave])){
                    $lines['notfreemium'][$chave]++;
                }else{
                    $lines['notfreemium'][$chave] = 1;
                }
                if(!isset($lines['freemium'][$chave])){
                    $lines['freemium'][$chave] = 0;
                }
            }
        }
        return ['calls'=>$lines];
    }
}
