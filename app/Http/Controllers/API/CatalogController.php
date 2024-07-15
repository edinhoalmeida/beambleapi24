<?php

namespace App\Http\Controllers\API;

use App\Jobs\ProcessImage;
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

class CatalogController extends BaseController
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
    *  path="/catalog/add",
    *  tags={"catalog 🔒"},
    *  @OA\RequestBody(
    *     @OA\MediaType(
    *      mediaType="multipart/form-data",
    *      @OA\Schema(ref="#/components/schemas/productrequest")
    *     )
    *   ),
    *  @OA\Response(
    *     response="200",
    *     description="A product list",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *          description="Status of request"
    *       ),
    *      @OA\Property(
    *          property="products",
    *          type="array",
    *          @OA\Items(ref="#/components/schemas/userproduct")
    *       ),
    *      @OA\Property(
    *          property="message",
    *          type="string",
    *          description="Messages from api"
    *      ),
    *     ),
    *  ),
    *  @OA\Response(
    *     response="400",
    *     description="Product add error",
    *     @OA\JsonContent(
    *      ref="#/components/schemas/baseerror",
    *     ),
    *  ),
  * )
  */
    public function add(ProductRequest $request)
    {
        $userid = auth()->user()->id;

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
            ProcessImage::dispatch($imageable)->onQueue('videos');
        }

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
  * @OA\Post(
    *  path="/catalog/update/{product_id}",
    *  tags={"catalog 🔒"},
    *  @OA\Parameter(
    *         name="product_id",
    *         description="A product id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *  ),
    *  @OA\RequestBody(
    *     @OA\MediaType(
    *      mediaType="multipart/form-data",
    *      @OA\Schema(ref="#/components/schemas/productrequest")
    *     )
    *   ),
    *  @OA\Response(
    *     response="200",
    *     description="A product list",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *          description="Status of request"
    *       ),
    *      @OA\Property(
    *          property="products",
    *          type="array",
    *          @OA\Items(ref="#/components/schemas/userproduct")
    *       ),
    *      @OA\Property(
    *          property="message",
    *          type="string",
    *          description="Messages from api"
    *      ),
    *     ),
    *  ),
    *  @OA\Response(
    *     response="400",
    *     description="Product update error",
    *     @OA\JsonContent(
    *      ref="#/components/schemas/baseerror",
    *     ),
    *  ),
  * )
  */
    public function update(Request $request, $product_id)
    {
        $userid = auth()->user()->id;

        $product_to_update = UserProducts::where('id', $product_id)->first();
        if(!empty($product_to_update->user_id) && $product_to_update->user_id!=$userid){
            return $this->sendError(__('beam.call_product_not_found'));
        }

        $all_sended = $request->all();

        $product_to_update->update($all_sended);
        // grava imagem
        $product_image = $request->get('product_image');
        if(!empty($product_image)) {
            $product_to_update->image()->delete();
            $imageable = new ImageModel([
                'base64' => $product_image,
                'modifier_id' => $product_to_update->id,
                'type' => 'product',
            ]);
            $product_to_update->image()->save($imageable);
            ProcessImage::dispatch($imageable)->onQueue('videos');
        }

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
    *  path="/catalog",
    *  tags={"catalog 🔒"},
    *  description="Returns products catalog",
    *  @OA\Response(
    *     response="200",
    *     description="A product list",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *          description="Status of request"
    *       ),
    *      @OA\Property(
    *          property="products",
    *          type="array",
    *          @OA\Items(ref="#/components/schemas/userproduct")
    *       ),
    *      @OA\Property(
    *          property="message",
    *          type="string",
    *          description="Messages from api"
    *      ),
    *     ),
    *  ),
    *  @OA\Response(
    *     response="400",
    *     description="Call or user id nvalid.",
    *     @OA\JsonContent(
    *      ref="#/components/schemas/baseerror",
    *     ),
    *  ),
  * )
  */
    public function list(Request $request)
    {
        $userid = auth()->user()->id;
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
  * @OA\Delete(
    *  path="/catalog/delete/{product_id}",
    *  tags={"catalog 🔒"},
    *  description="Delete a product",
    *  @OA\Parameter(
    *         name="product_id",
    *         description="A product id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *  ),
    *  @OA\Response(
    *     response="200",
    *     description="A product list",
    *     @OA\JsonContent(
    *        @OA\Property(
    *          property="success",
    *          type="boolean",
    *          description="Status of request"
    *       ),
    *      @OA\Property(
    *          property="products",
    *          type="array",
    *          @OA\Items(ref="#/components/schemas/userproduct")
    *       ),
    *      @OA\Property(
    *          property="message",
    *          type="string",
    *          description="Messages from api"
    *      ),
    *     ),
    *  ),
    *  @OA\Response(
    *     response="400",
    *     description="Call or user id nvalid.",
    *     @OA\JsonContent(
    *      ref="#/components/schemas/baseerror",
    *     ),
    *  ),
  * )
  */
    public function delete(Request $request, $product_id)
    {
        $userid = auth()->user()->id;
        $product_to_delete = UserProducts::where('id', $product_id)->first();
        if(!empty($product_to_delete->user_id) && $product_to_delete->user_id!=$userid){
            return $this->sendError(__('beam.call_product_not_found'));
        } else {
            $product_to_delete->delete();
        }

        $products = UserProducts::where('user_id', $userid)->get();
        $productss = UserProductResource::collection($products);
        $response = [
            'success' => true,
            'products' => $productss,
            'message' => "products list ok"
        ];
        return response()->json($response, 200);
    }

}
