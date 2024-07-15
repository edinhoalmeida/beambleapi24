<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\Videocall;
use App\Models\VideocallProducts;
use App\Models\UserUserProducts;
use App\Models\UserProducts;
use App\Models\ImageB64 as ImageModel;
use App\Http\Resources\VideocallProduct as VideocallProductResource;
use App\Http\Resources\UserProduct as UserProductResource;
use App\Http\Requests\ProductRequest;

use App\Events\ProductOffered;

use App\Libs\Checkout;

class ShopperController extends BaseController
{
    public function __construct()
    {
        // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','show']]);
        // $this->middleware('permission:user-create', ['only' => ['create','store']]);
        // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /**
  * @OA\Post(
  *     path="/shopper/{call_id}/product/add",
  *     tags={"shopper 🔒"},
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\RequestBody(
  *        @OA\MediaType(
  *         mediaType="multipart/form-data",
  *         @OA\Schema(ref="#/components/schemas/productrequest")
  *        )
  *      ),
  *     @OA\Response(
  *        response="200",
  *        description="Get a status of a added product",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppingsingle",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Product add error",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function add(ProductRequest $request, $call_id)
    {
        $userid = auth()->user()->id;

        $Videocall = Videocall::where('beamer_id', $userid)
            ->where('id', $call_id)->first();

        if(empty($Videocall)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendError($dados, __('beam.call_not_found'));
        }

        $product_price = $request->get('product_price');
        $product_currency = $request->get('product_currency','eur');
        $product_weight = $request->get('product_weight');
        $product_size = $request->get('product_size');
        $title = $request->get('title');
        $description = $request->get('description');
        $brand_name = $request->get('brand_name');
        $color = $request->get('color');
        $fabric = $request->get('fabric');
        $condition = $request->get('condition');
        $size = $request->get('size');


        $product = UserProducts::create([
            'user_id' => $userid,
            'product_price' => $product_price,
            'product_currency' => $product_currency,
            'product_weight' => $product_weight,
            'product_size' => $product_size,
            'title' => $title,
            'description' => $description,
            'brand_name' => $brand_name,
            'color' => $color,
            'fabric' => $fabric,
            'condition' => $condition,
            'size' => $size,
        ]);
        // grava imagem
        $product_image = $request->get('product_image');
        if(!empty($product_image)) {
            $imageable = new ImageModel([
                'base64' => $product_image,
                'modifier_id' => $product->id,
                'type' => 'product',
            ]);
            $product->image()->save($imageable);
        }

        $call_product = VideocallProducts::create([
            'videocall_id' => $call_id,
            'product_id' => $product->id,
            'status' => 'new'
        ]);

        dblog('adicionando produto na chamada', $product->id);

        $user_user_product = UserUserProducts::create([
            'videocall_id' => $call_id,
            'product_id' => $product->id,
            'client_id' => $Videocall->client_id,
            'beamer_id' => $userid,
            'status' => 'new'
        ]);

        // if($status == 'all') {
        //     $products = VideocallProducts::where('videocall_id', $call_id)->get();
        // } else {
        //     $products = VideocallProducts::where('videocall_id', $call_id)
        //         ->where('status', $status)->get();
        // }
        VideocallProducts::persist_firebase_db($call_id);       

        // trigger event product add
        // disable for now
        // event(new ProductOffered($Videocall, $call_product));

        $product = new VideocallProductResource($call_product);
        $response = [
            'product' => $product
        ];
        return $this->sendResponse($response, "Product added");
    }
    /**
  * @OA\Get(
  *     path="/shopper/beamer/catalog",
  *     tags={"shopper 🔒"},
  *     description="Returns products catalog",
  *     @OA\Response(
  *        response="200",
  *        description="A product list",
  *        @OA\JsonContent(
      *       @OA\Property(
     *          property="success",
     *          type="boolean",
     *          description="Status of request"
     *       ),
    *       @OA\Property(
    *           property="products",
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/userproduct") 
    *        ),
     *      @OA\Property(
     *          property="message",
     *          type="string",
     *          description="Messages from api"
     *      ),
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or user id nvalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function listCatalog(Request $request)
    {
        $userid = auth()->user()->id;
        // $product = UserProducts::create([
        //     'user_id' => $userid,
        //     'product_price' => $product_price,
        //     'product_currency' => $product_currency,
        //     'product_weight' => $product_weight,
        //     'product_size' => $product_size,
        //     'title' => $title,
        //     'description' => $description,
        // ]);
        $products = UserProducts::where('user_id', $userid)->get();
        $productss = UserProductResource::collection($products);
        $response = [
            'success' => true,
            'products' => $productss,
            'message' => "products list ok"
        ];
        return response()->json($response, 200);
    }
/**
  * @OA\Get(
  *     path="/shopper/{call_id}/products/new",
  *     tags={"shopper 🔒"},
  *     description="Returns products made available by beamer still in new status",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Response(
  *        response="200",
  *        description="A product list",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppinglist",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or user id nvalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function listNew(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        return $this->list_status($call_id, $userid, 'new');
    }

    /**
  * @OA\Get(
  *     path="/shopper/{call_id}/products/all",
  *     tags={"shopper 🔒"},
  *     description="Returns products made available by the beamer in any status",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Response(
  *        response="200",
  *        description="A product list",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppinglist",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or user id nvalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function listAll(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        return $this->list_status($call_id, $userid, 'all');
    }

    public function list_status($call_id, $userid, $status = 'all')
    {
        $Videocall = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->count();

        if(empty($Videocall)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendError($dados, __('beam.call_not_found'));
        }

        if($status == 'all') {
            $products = VideocallProducts::where('videocall_id', $call_id)
                ->get();
        } else {
            $products = VideocallProducts::where('videocall_id', $call_id)
                ->where('status', $status)->get();
        }
        $products = VideocallProductResource::collection($products);
        $response = [
            'products' => $products
        ];
        return $this->sendResponse($response, "products list ok");
    }


    public function _call_product_test(Request $request, $call_id, $product_id)
    {
        $userid = auth()->user()->id;
        $Videocall = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->count();
        if(empty($Videocall)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendError($dados, __('beam.call_not_found'));
        }
        $product = VideocallProducts::where('videocall_id', $call_id)->where('product_id', $product_id)->first();
        if(empty($product)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendError($dados, __('beam.call_product_not_found'));
        }
        return $product;
    }

/**
  * @OA\Get(
  *     path="/shopper/{call_id}/{product_id}/confirm",
  *     tags={"shopper 🔒"},
  *     description="Client add the product to shopping cart",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Parameter(
  *        name="product_id",
  *        description="A product id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Response(
  *        response="200",
  *        description="A updated product list",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppinglist",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or Product id invalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function confirm(Request $request, $call_id, $product_id)
    {
        $product = $this->_call_product_test($request, $call_id, $product_id);
        if($product instanceof VideocallProducts) {
            // ok. is a product line
        } else {
            // so is a error returned
            return $product;
        }
        $product->status = 'accepted';
        $product->client_accepted_at = date("Y-m-d H:i:s");
        $product->save();

        VideocallProducts::persist_firebase_db($call_id);

        $products = VideocallProducts::where('videocall_id', $call_id)->get();
        $products = VideocallProductResource::collection($products);
        $response = [
            'products' => $products
        ];
        return $this->sendResponse($response, "product accepted");
    }
/**
  * @OA\Get(
  *     path="/shopper/{call_id}/{product_id}/reject",
  *     tags={"shopper 🔒"},
  *     description="Client reject the product",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Parameter(
  *        name="product_id",
  *        description="A product id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Response(
  *        response="200",
  *        description="A updated product list",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppinglist",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or Product id invalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function reject(Request $request, $call_id, $product_id)
    {
        $product = $this->_call_product_test($request, $call_id, $product_id);
        if($product instanceof VideocallProducts) {
            // ok. is a product line
        } else {
            // so is a error returned
            return $product;
        }
        $product->status = 'rejected';
        $product->client_accepted_at = null;
        $product->save();

        VideocallProducts::persist_firebase_db($call_id);

        $products = VideocallProducts::where('videocall_id', $call_id)->get();
        $products = VideocallProductResource::collection($products);
        $response = [
            'products' => $products
        ];
        return $this->sendResponse($response, "product rejected");
    }

/**
  * @OA\Get(
  *     path="/shopper/{call_id}/{product_id}/read",
  *     tags={"shopper 🔒"},
  *     description="Set a status of a product from 'new' to 'read'",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Parameter(
  *        name="product_id",
  *        description="A product id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\Response(
  *        response="200",
  *        description="A updated product list",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/shoppinglist",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or Product id invalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function product_read(Request $request, $call_id, $product_id)
    {
        $product = $this->_call_product_test($request, $call_id, $product_id);
        if($product instanceof VideocallProducts) {
            // ok. is a product line
        } else {
            // so is a error returned
            return $product;
        }
        // only updates the product if it had the 'new' status
        if($product->status == 'new') {
            $product->status = 'read';
            $product->save();
        }

        VideocallProducts::persist_firebase_db($call_id);

        $products = VideocallProducts::where('videocall_id', $call_id)->get();
        $products = VideocallProductResource::collection($products);
        $response = [
            'products' => $products
        ];
        return $this->sendResponse($response, "product read");
    }

    # TODO: rota não usada
    public function clientDetails(Request $request, $call_id)
    {
        $userid = auth()->user()->id;

        $Videocall = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->count();

        if(empty($Videocall)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendResponse($dados, __('beam.call_not_found'));
        }

        $product_id = $request->get('product_id');
        $client_details = $request->get('client_details');

        $product = VideocallProducts::where('videocall_id', $call_id)
                ->where('id', $product_id)
                ->first();

        if(empty($product)) {
            $dados = [];
            $dados['product'] = [];
            return $this->sendResponse($dados, 'product_id not found');
        }

        $product->client_details = $client_details;
        $product->save();
        $product = new VideocallProductResource($product);
        $response = [
            'product' => $product
        ];
        return $this->sendResponse($response, "product updated");
    }

/**
  * @OA\Post(
  *     path="/shopper/{call_id}/checkout",
  *     tags={"shopper 🔒"},
  *     description="Returns products aceppted, shipping cost and checkout details",
  *     @OA\Parameter(
  *        name="call_id",
  *        description="A call id number",
  *        in="path",
  *        required=true,
  *        @OA\Schema(
  *          type="integer"
  *        )
  *     ),
  *     @OA\RequestBody(
  *        @OA\MediaType(
  *         mediaType="multipart/form-data",
  *         @OA\Schema(ref="#/components/schemas/checkoutrequest")
  *        )
  *      ),
  *     @OA\Response(
  *        response="200",
  *        description="A checkout details",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/checkout",
  *        ),
  *     ),
  *     @OA\Response(
  *        response="400",
  *        description="Call or user id nvalid.",
  *        @OA\JsonContent(
  *         ref="#/components/schemas/baseerror",
  *        ),
  *     ),
  * )
  */
    public function checkout(Request $request, $call_id)
    {
        $userid = auth()->user()->id;
        $Videocall = Videocall::where('client_id', $userid)
            ->where('id', $call_id)->withTrashed()->first();

        if(empty($Videocall)) {
            $dados = [];
            $dados['products'] = [];
            return $this->sendError($dados, __('beam.call_not_found'));
        }

        $received = $request->all();
        if(!empty($received['products_accepted'])){
            if(is_string($received['products_accepted'])){
                $received['products_accepted'] = explode(",",$received['products_accepted']);
            }
            $vcp = VideocallProducts::where('videocall_id', $call_id)->whereIn('product_id', $received['products_accepted'])->get();
            foreach($vcp as $vc){
                $vc->status = 'accepted';
                $vc->client_accepted_at = date("Y-m-d H:i:s");
                $vc->save();
            }
        }

        $products = VideocallProducts::where('videocall_id', $call_id)
                ->where('status', 'accepted')->get();

        $call_cost = 0;

        $checkout = (new Checkout($products, $call_id, $Videocall->beamer_id, $userid, $call_cost))->get();

        $address = auth()->user()->shipping_address();

        $checkout['address'] = $address;
        // mocked
        $checkout['wallet'] = ['wallet_balance' => 000];

        $products = VideocallProductResource::collection($products);
        $response = [
            'products' => $products
        ];
        $response = array_merge($response, $checkout);
        return $this->sendResponse($response, "checkout list ok");
    }

}
