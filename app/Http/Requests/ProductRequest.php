<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

use App\Rules\Strbase64;
/**
 * @OA\Schema(
 *   schema="productrequest",
 *   required={"title","brand_name","description","product_price","product_image","product_weight","product_size"},
     * @OA\Property(
     *   property="brand_name",
     *   type="string",
     *   description="Brand name"
     * ),
     * @OA\Property(
     *   property="title",
     *   type="string",
     *   description="Product name"
     * ),
     * @OA\Property(
     *   property="color",
     *   type="string",
     *   description="Color"
     * ),
     * @OA\Property(
     *   property="fabric",
     *   type="string",
     *   description="Fabric"
     * ),
     * @OA\Property(
     *   property="condition",
     *   type="string",
     *   description="Condition"
     * ),
     * @OA\Property(
     *   property="size",
     *   type="string",
     *   description="Size of product"
     * ),
     * @OA\Property(
     *   property="description",
     *   type="string",
     *   description="Description"
     * ),
     * @OA\Property(
     *   property="product_price",
     *   type="integer",
     *   nullable=true, 
     *   description="Product price in cents. Ex: 45.44 (EUR) should be 4544"  
     * ),
     * @OA\Property(
     *   property="product_size", 
     *   type="string",
     *   nullable=false, 
     *   description="S or M or L or XL (size of product packaging)"
     * ),
     * @OA\Property(
     *   property="product_weight", 
     *   type="number",
     *   nullable=false, 
     *   description="Product weight in Kilograms"
     * ),
     * @OA\Property(
     *   property="product_currency",
     *   type="string",
     *   description="Default is eur"
     * ),
     * @OA\Property(
     *   property="product_image",
     *   type="string",
     *   description="Base64 encoded product image"
     * ),
 * ),
 */
class ProductRequest extends FormRequest
{
    private $sizes_avaible;
    private $weight_max;
    
    public function rules()
    {
        $sizes = config('shipping.uber.sizes');
        $sizes_keys = array_keys($sizes);
        $this->sizes_avaible = implode(', ', $sizes_keys);
        $this->weight_max = config('shipping.uber.weight.max');
        return [
            'title' => 'required',
            'brand_name' =>'required',
            'color' =>'',
            'fabric' =>'',
            'condition' =>'',
            'size' =>'',
            'description' => 'required',
            'product_price'=>'required|integer',
            'product_currency' => '',
            'product_image'=>['required', new Strbase64],
            'product_weight'=>'required|decimal:2|max:' . $this->weight_max,
            'product_size'=>[
                'required',
                Rule::in($sizes_keys),
            ],
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ]);
        $response->setStatusCode(400);
        throw new HttpResponseException($response);
    }

    public function messages()
    {
        return [
            'product_weight.required' => 'Product weight is required',
            'product_weight.decimal' => 'Product weight should be 9.99 format',
            'product_weight.max' => 'Product weight max is ' . $this->weight_max,
            'product_size.required' => 'Product size is required',
            'product_size.in' => 'Product size should be one of these: ' . $this->sizes_avaible
        ];
    }
}
