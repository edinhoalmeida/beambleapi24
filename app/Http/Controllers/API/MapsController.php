<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Arr;

use App\Libs\Beamer;
use App\Models\Videocall;
use App\Http\Resources\Pins as PinsResource;
use App\Http\Resources\PinsSummary as PinsSummaryResource;
use App\Http\Resources\Tendence as TendenceResource;

use Illuminate\Support\Facades\Cache;

class MapsController extends Controller
{

    function __construct()
    {
        // Videocall::clearExpiredCalls();
    }

/**
    * @OA\Post(
    *     path="/search_beamer",
    *     description="Search active beamers",
    *     tags={"maps and feed"},
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="category",
    *                      type="integer",
    *                      description="(optional)"
    *                  ),
    *                  @OA\Property(
    *                      property="keyword",
    *                      type="string",
    *                      description="(optional)"
    *                  ),
    *                  @OA\Property(
    *                      property="tabs",
    *                      type="string",
    *                      description="following or discover"
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lat0]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lng0]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lat1]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lng1]",
    *                      type="number",
    *                      nullable=true,
    *                  )
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Get list of bearmer online",
    *          @OA\JsonContent(
    *              ref="#/components/schemas/basepins"
    *          ),
    *     )
    * )
    */
    public function search_beamer(Request $request, $debug = false)
    {
        $user = auth()->user();
        if(!empty($user->id)){
            Beamer::$logged_userid = $user->id;
            PinsResource::$logged_userid = $user->id;
        }

        // $category_id = $request->input('beamer_type');
        $category_id = $request->input('category');

        // discover/following
        $tabs = $request->input('tabs', 'discover');

        $dados = $request->input('cords');
        if(empty($dados['lat0'])){
            $dados['lat0']=90;
            $dados['lat1']=-90;
            $dados['lng0']=90;
            $dados['lng1']=-90;
        }

        $keyword = $request->input('keyword');

        $locais = Beamer::by_viewport_general(
            $dados['lat0'],
            $dados['lng0'],
            $dados['lat1'],
            $dados['lng1'],
            keyword: $keyword,
            category_id: $category_id,
            tabs: $tabs
        );

        // $tendence = Beamer::tendence_by_viewport($dados['lat0'], $dados['lng0'], $dados['lat1'], $dados['lng1'], $artist_id, $debug);

        $response = [
            'success' => true,
            'data'    => [
                'pins' => PinsResource::collection($locais),
                // 'tendences' => TendenceResource::collection($tendence)
            ]
        ];
        return response()->json($response, 200);
    }

    public function searchBeamerByCategory(Request $request, $debug = false)
    {
        $user = Auth::user();
        // $beamer_type = $request->input('beamer_type', 'shop');
        $category = $request->input('category');

        if(!empty($user->id)){
            Beamer::$logged_userid = $user->id;
            PinsResource::$logged_userid = $user->id;
        }

        $locais = Beamer::by_word(category_id: $category);
        $response = [
            'success' => true,
            'data'    => [
                'pins' => PinsResource::collection($locais),
            ]
        ];
        return response()->json($response, 200);
    }

    public function searchBeamerByWords(Request $request, $debug = false)
    {
        $user = Auth::user();
        $category_id = $request->input('category');
        $keyword = $request->input('keyword');

        if(!empty($user->id)){
            Beamer::$logged_userid = $user->id;
            PinsResource::$logged_userid = $user->id;
        }

        $locais = Beamer::by_word($keyword, $category_id, $debug);
        $response = [
            'success' => true,
            'data'    => [
                'pins' => PinsResource::collection($locais),
            ]
        ];
        return response()->json($response, 200);
    }

    public function searchToFeed(Request $request, $debug = false)
    {
        $user = Auth::user();
        $category_id = $request->input('category');
        $keyword = $request->input('keyword');

        if(!empty($user->id)){
            Beamer::$logged_userid = $user->id;
            PinsResource::$logged_userid = $user->id;
        }

        $data_submited = $request->all();
        $data_submited = md5(serialize($data_submited));
        $per_page = $request->has('itemsPerPage') ? $request->input('itemsPerPage') : config('thisapp.pagination_per_page', 16);
        $current_page = $request->input("page") ?? 1;
        $page_first_result = ($current_page-1) * $per_page;
        if( $pagination = Cache::get($data_submited) ) {
            $locais = Beamer::by_word(tabs: null, category_id: $category_id,
                keyword: $keyword,
            limit: $per_page,
            offset: $page_first_result);
            $pagination['currentPage'] = $current_page;
            $pagination['countFromCache'] = true;
        } else {
            $locais = Beamer::by_word(tabs: null, category_id: $category_id, keyword: $keyword);
            $total = $locais->count();
            $pagination = [
                "totalItems" => $total,
                "itemsPerPage" => $per_page,
                "totalPages" => (int) ceil($total / $per_page),
                "countFromCache" => false
            ];
            Cache::put($data_submited, $pagination, $seconds = 20);
            $pagination['currentPage'] = $current_page;
            $locais = Beamer::by_word(null, $category_id, limit: $per_page, offset: $page_first_result);
        }

        // $locais = Beamer::by_word(null, $category_id);


        $response = [
            'success' => true,
            'data'    => [
                'pins' => PinsResource::collection($locais),
                'pagination' => $pagination,
            ]
        ];
        return response()->json($response, 200);
    }

    /**
    * @OA\Post(
    *     path="/search_beamer_summary",
    *     description="Search active beamers",
    *     tags={"maps and feed"},
    *     @OA\RequestBody(
    *          @OA\MediaType(
    *              mediaType="multipart/form-data",
    *              @OA\Schema(
    *                  @OA\Property(
    *                      property="category",
    *                      type="integer",
    *                      description="(optional)"
    *                  ),
    *                  @OA\Property(
    *                      property="keyword",
    *                      type="string",
    *                      description="(optional)"
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lat0]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lng0]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lat1]",
    *                      type="number",
    *                      nullable=true,
    *                  ),
    *                  @OA\Property(
    *                      property="cords[lng1]",
    *                      type="number",
    *                      nullable=true,
    *                  )
    *              )
    *          )
    *      ),
    *     @OA\Response(
    *          response="200",
    *          description="Get list of bearmer online",
    *          @OA\JsonContent(
    *              ref="#/components/schemas/basepins"
    *          ),
    *     )
    * )
    */
    public function search_beamer_summary(Request $request)
    {
        $user = auth()->user();
        if(!empty($user->id)){
            Beamer::$logged_userid = $user->id;
            PinsResource::$logged_userid = $user->id;
        }

        $category_id = $request->input('category');
        $keyword = $request->input('keyword');

        // discover/following
        $tabs = null;

        $msg =<<<EOD
            ================================lat0, lng0
            |                                        |
            |       ERROR ON variable 'cords'        |
            |                                        |
            |                                        |
            |       EXAMPLE                          |
            |                                        |
            |       //cords[lat0]:80.5547813857      |
            |       //cords[lng0]:46.54764786        |
            |       //cords[lat1]:-60.578225677094   |
            |       //cords[lng1]:-46.748895224384   |
            |                                        |
            |                                        |
            lat1, lng1 ================================
EOD;
        $dados = $request->input('cords');
        if(empty($dados['lat0'])){
            return $this->sendError( $msg );
        }
        $lat0 = floatval($dados['lat0']);
        $lat1 = floatval($dados['lat1']);
        $lng0 = floatval($dados['lng0']);
        $lng1 = floatval($dados['lng1']);



        if($lat0!=null && $lng0!=null && $lat1!=null && $lng1!=null){
            if($lat0<$lat1){
                return $this->sendError( $msg );
            }
            if($lng0<$lng1){
                return $this->sendError( $msg );
            }
        } else {
            return $this->sendError( $msg );
        }

        $locais = Beamer::by_viewport_general(
            $dados['lat0'],
            $dados['lng0'],
            $dados['lat1'],
            $dados['lng1'],
            keyword: $keyword,
            category_id: $category_id,
            tabs: $tabs
        );

        $pins_ar = PinsSummaryResource::collection($locais);
        $pins_hashed_map = [];
        foreach($pins_ar as $pin) {
            $pins_hashed_map[ $pin['user_id'] ] = $pin;
        }
        $response = [
            'success' => true,
            'data'    => [
                'summaries' => $pins_hashed_map
            ]
        ];
        return response()->json($response, 200);
    }

    public function sendError($error, $code = 400)
    {

        // 404 Not Found
        // 401 Unauthorized
        // 403 Forbidden
        // 400 Bad Request

        $response = [
            'success' => false
        ];
        $error = str_replace("\r", '', $error);
        $lines = explode("\n", $error);
        foreach ($lines as $i=>$line) {
            $response[str_pad('message' . $i, 10, "-")] = trim($line);
        }
        return response()->json($response, $code);
    }

}
