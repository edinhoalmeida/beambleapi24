<?php
namespace App\Libs;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

use App\Models\UserAddresses;
use App\Models\UserTrack;
use App\Models\User;

use DB;

class Beamer {

    public static $logged_userid = null;

    public static function by_viewport_general($lat0, $lng0, $lat1, $lng1, $keyword = null, $category_id = null, $tabs = null)
    {
        $inside_map_only = true;
        $locais = self::by_word($tabs, $keyword, $category_id, $lat0, $lng0, $lat1, $lng1, $inside_map_only);
        return $locais;
    }

    public static function beamer_by_id($user_id)
    {
        $locais = UserTrack::select(\DB::raw("
                users_track.user_id
                , users_track.lat
                , users_track.lng
                , users_track.beamer_type
                , users_track.cost_per_minute
                , users_track.with_donation
                , users_track.is_freemium
                , users_track.event_title
                , users_track.categories
                , users_track.keywords as track_keywords
                , users.name
                , users.uuid
                , CASE
                    WHEN
                        users_track.status = 'on'
                        THEN 1
                    ELSE 0
                END as online
                , COALESCE(users.surname, '') as surname
                , COALESCE(users.keywords, '') as keywords
            "))
            ->join('users', 'users.id', '=', 'users_track.user_id')
            // ->where('users_track.status', '=', 'on')
            ->leftJoin('user_followers', function ($join) {
                $join->on('user_followers.user_id', '=', 'users_track.user_id')
                ->where('user_followers.follower_id', '=', empty(Beamer::$logged_userid) ? 10000000000000 : Beamer::$logged_userid)
                ->whereNull('user_followers.deleted_at');
            });

        // $locais->orderBy('users_track.updated_at', 'DESC');
        $locais = $locais
            ->where('users_track.user_id', $user_id)
            ->where('users_track.last_one', 1)
            ->limit(1)->get();
        return $locais;
    }

    public static function beamer_empty_by_id($user_id)
    {
        $locais = User::select(\DB::raw("
                    users.id as 'user_id'
                , null as 'lat'
                , null as 'lng'
                , null as 'beamer_type'
                , null as 'cost_per_minute'
                , null as 'with_donation'
                , null as 'is_freemium'
                , null as 'event_title'
                , null as 'categories'
                , null as 'track_keywords'
                , users.name
                , users.uuid
                , 0 as 'online'
                , COALESCE(users.surname, '') as surname
                , COALESCE(users.keywords, '') as keywords
            "));
        $locais = $locais->where('users.id', $user_id)->limit(1)->get();
        return $locais;
    }

    public static function by_word(
        $tabs=null, $keyword=null,
        $category_id=null,
        $lat0=null, $lng0=null, $lat1=null, $lng1=null,
        $inside_map_only = false,
        $offset = 0, $limit = null)
    {
        $ssql_db_mode = DB::select("SELECT @@sql_mode as ssql_mode");
        $ssql_db_mode = explode(',', $ssql_db_mode[0]->ssql_mode);
        if (($ssql_key = array_search('ONLY_FULL_GROUP_BY', $ssql_db_mode)) !== false) {
            unset($ssql_db_mode[$ssql_key]);
            $ssql_db_mode = implode(",", $ssql_db_mode);
            Db::statement("SET SESSION sql_mode = '" . $ssql_db_mode . "'");
            unset($ssql_db_mode);
        }

        $with_cords = false;
        $with_words = false;
        $with_cats = false;
        $to_feed = false;

        if($lat0!=null && $lng0!=null && $lat1!=null && $lng1!=null){
            /*
            ================================lat0, lng0
            |                                        |
            |                                        |
            |                                        |
            |                                        |
            |                                        |
            |                                        |
            |                                        |
            |                                        |
            lat1, lng1 ================================
            */
            $lat0 = floatval($lat0);
            $lat1 = floatval($lat1);
            $lng0 = floatval($lng0);
            $lng1 = floatval($lng1);

            if($lat0<$lat1){
                die("erro latitude $lat0<$lat1 ");
            }
            if($lng0<$lng1){
                die("erro longitude $lng0<$lng1 ");
            }
            $with_cords = true;
        }


        $contagem = [];

//        add relevance by keyword
//        if(!empty($keyword)){
//            $keywords = explode(" ", trim($keyword));
//            $count_line = "(COALESCE(CAST((%s like '%%%s%%') as SIGNED),0) * %u)";
//
//            $relcount = "(";
//            if(count($keywords)==1){
//                $relcount .= sprintf($count_line,'users_track.keywords', $keywords[0],100);
//                $relcount .= " + ";
//                $relcount .= sprintf($count_line,'users.keywords', $keywords[0],50);
//            } else {
//                $blocks = [];
//                foreach($keywords as $keyword) {
//                    $blocks[] = sprintf($count_line,'users_track.keywords', $keyword,100);
//                    $blocks[] = sprintf($count_line,'users.keywords', $keyword,50);
//                }
//                $relcount .= implode(" + ", $blocks);
//            }
//            $relcount .=")";
//            $contagem[] = $relcount;
//            $with_words = true;
//        }

        if(!empty($keyword)){
            $keywords = explode(" ", trim($keyword));
            $with_words = true;
        }


        if(!empty($category_id)){
            $categories = ":".$category_id.":";
            $count_line = "(COALESCE(CAST((%s like '%%%s%%') as SIGNED),0) * %u)";
            //$catcount = "(";
            $catcount = sprintf($count_line,'users_track.categories', $categories, 100);
            //$catcount .=")";
            $contagem[] = $catcount;
            $with_cats = true;
        }

        $contagem[] = "(CASE
            WHEN
                users_track.keywords IS NULL AND users.keywords IS NULL
                THEN 0
            WHEN users_track.keywords IS NOT NULL AND users.keywords IS NULL
                THEN 30
            WHEN users_track.keywords IS NULL AND users.keywords IS NOT NULL
                THEN 20
            ELSE 40
        END)";
        $contagem[] = "(CASE
                WHEN
                    user_followers.id IS NOT NULL
                    THEN 100
                ELSE 0
            END)";


        if(empty($contagem)){
            $relcount = '';
        } else {
            $relcount = ", ( " . implode("\n+\n", $contagem) . ") as relevance";
        }
        $locais = UserTrack::select(\DB::raw("
                users_track.user_id
                , users_track.lat
                , users_track.lng
                , users_track.beamer_type
                , users_track.cost_per_minute
                , users_track.with_donation
                , users_track.is_freemium
                , users_track.event_title
                , users_track.categories
                , users_track.keywords as track_keywords
                , users_beamer_data.name
                , users.uuid
                , CASE
                    WHEN
                        users_track.status = 'on'
                        THEN 1
                    ELSE 0
                END as online
                , COALESCE(users.keywords, '') as keywords
                , COALESCE(users_beamer_data.surname, '') as surname
                , user_followers.id
                ".$relcount."
            "))
            ->join('users', 'users.id', '=', 'users_track.user_id')
            ->join('users_beamer_data', 'users.id', '=', 'users_beamer_data.user_id')
            ->leftJoin('user_followers', function ($join) {
                $join->on('user_followers.user_id', '=', 'users_track.user_id')
                ->where('user_followers.follower_id', '=', empty(Beamer::$logged_userid) ? 10000000000000 : Beamer::$logged_userid)
                ->whereNull('user_followers.deleted_at');
            });

        if(!empty($tabs) && $tabs=='following' && !empty(Beamer::$logged_userid)){
            // following
            $locais->join('user_followers as uf2', function ($join) {
                $join->on('uf2.user_id', '=', 'users_track.user_id')
                ->where('uf2.follower_id', '=', Beamer::$logged_userid)
                ->whereNull('uf2.deleted_at');
            });
        } else {
            // discover
        }
        if($with_words){
//            $locais->orWhere(function ($query) use ($keywords, $with_cords) {
//                $first = array_pop($keywords);
//                $query->where('users_track.keywords', 'LIKE', '%'.$first.'%');
//                $query->orWhere('users.keywords', 'LIKE', '%'.$first.'%');
//                foreach($keywords as $keyword) {
//                    $query->orWhere('users_track.keywords', 'LIKE', '%'.$keyword.'%');
//                    $query->orWhere('users.keywords', 'LIKE', '%'.$keyword.'%');
//                }
//                // if(!$with_cords){
//                    $query->orWhere('users_track.keywords', '=', NULL);
//                    $query->orWhere('users.keywords', '=', NULL);
//                // }
//            });
            $locais->where(function ($query) use ($keywords) {
                $first = array_pop($keywords);
                $query->where('users_beamer_data.name', 'LIKE', '%'.$first.'%');
                $query->orWhere('users_beamer_data.surname', 'LIKE', '%'.$first.'%');
                foreach($keywords as $keyword) {
                    $query->orWhere('users_beamer_data.name', 'LIKE', '%'.$keyword.'%');
                    $query->orWhere('users_beamer_data.surname', 'LIKE', '%'.$keyword.'%');
                }
            });
        }

        $locais->where('users_track.last_one', '=', 1);

        // dd(EXCLUDE_USERS);
        if(!empty(EXCLUDE_USERS)){
            $locais->whereNotIn('users_track.user_id', EXCLUDE_USERS);
        }
        // dd($locais->get());

        if($with_cords){
            $locais->where('users_track.lat', '<=', $lat0)
                ->where('users_track.lng', '<=', $lng0)
                ->where('users_track.lat', '>=', $lat1)
                ->where('users_track.lng', '>=', $lng1);
        }

        // new category
        $category_id = empty($category_id) ? null : (int) $category_id;
        if($category_id=='tous' || $category_id=='all'){
            $category_id = null;
        }
        if(!empty($category_id)){
            $locais->where('users_track.categories', 'LIKE', '%:'.$category_id.':%');
        }

        $locais->orderBy('relevance', 'DESC');
        $locais->orderBy('users_track.created_at', 'DESC');

        // echo $locais->toSql();
        // die();
        // OLD category was classic
        // $category_id = empty($category_id) ? null : strtolower($category_id);
        // if($category_id=='tous' || $category_id=='all'){
        //     $category_id = null;
        // }
        // if(!empty($category_id)){
        //     $locais->where('users_track.beamer_type', '=', $category_id);
        // }


        if(!empty(Beamer::$logged_userid)){
            // $locais->where('users_track.user_id', '<>', Beamer::$logged_userid);
        }

        // if(empty($category_id) && !$with_words && !$with_cords){
        //     $locais->orderBy('users_track.created_at', 'DESC');
        // }
//        echo $locais->toSql();
//        die();
        if($limit){
            $locais->offset($offset)->limit($limit);
            // echo $locais->toSql();
            // die();
            return $locais->get();
        }
        return $locais->get();
    }

    public static function short_beamer_by_id($user_id)
    {
        $locais = UserTrack::select(\DB::raw("
                users_track.user_id
                , users_track.categories
                , users_beamer_data.name
                , users.uuid
                , CASE
                    WHEN
                        users_track.status = 'on'
                        THEN 1
                    ELSE 0
                END as online
                , COALESCE(users_beamer_data.surname, '') as surname
            "))
            ->join('users', 'users.id', '=', 'users_track.user_id')
            ->join('users_beamer_data', 'users.id', '=', 'users_beamer_data.user_id');
        $locais->whereNull('users_beamer_data.deleted_at');

        $locais->orderBy('users_track.updated_at', 'DESC');
        $locais = $locais->where('users_track.user_id', $user_id)->withTrashed()->limit(1)->first();
        return $locais;
    }

}
