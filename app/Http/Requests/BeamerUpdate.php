<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/*
postman format

company_name:LFF
email:edinhoalmeida@gmail.com
name:Edinho Almeida
my_language[]:fr
my_language[]:en
password:123456
phone:551199293123
store_address:Rua coronel oscar porto, 932
store_postal_code:04003-005
store_city:São Paulo
store_country:Brasil
company_doc:123123123123
company_type:instore|freelance
tos_accepted:yes
Foto
Logo

*/
/**
 * @OA\Schema(
 *   schema="beamerupdate",
     * @OA\Property(
     *   property="company_name",
     *   type="string",
     *   description="Company name"
     * ),
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="Seller name"
     * ),
     * @OA\Property(
     *   property="surname",
     *   type="string",
     *   description="Seller surname"
     * ),
     * @OA\Property(
     *   property="my_language[]",
     *   type="string",
     *   description="Language. It can be my_language:fr also."
     * ),
     * @OA\Property(
     *   property="password",
     *   type="string",
     *   description="Password"
     * ),
     * @OA\Property(
     *   property="phone",
     *   type="string",
     * ),
     * @OA\Property(
     *   property="store_address",
     *   type="string",
     *   description="Store address"
     * ),
     * @OA\Property(
     *   property="store_postal_code",
     *   type="string",
     *   description="Store postal_code"
     * ),
     * @OA\Property(
     *   property="store_city",
     *   type="string",
     *   description="Store city"
     * ),
     * @OA\Property(
     *   property="store_country",
     *   type="string",
     *   description="Store country"
     * ),
     * @OA\Property(
     *   property="company_doc",
     *   type="string",
     *   description="Store document number (SIRET)"
     * ),
     * @OA\Property(
     *   property="accept_parcel_return",
     *   type="integer",
     *   nullable=true, 
     *   description="Accept parcel return. Should be 0 or 1"
     * ),
     * @OA\Property(
     *   property="second_hand_resaler",
     *   type="integer",
     *   nullable=true, 
     *   description="second hand resaler. Should be 0 or 1, default is 0"
     * ),
     * @OA\Property(
     *   property="level_expertise",
     *   type="string",
     *   nullable=true, 
     * ),
     * @OA\Property(
     *   property="company_type",
     *   type="enum['instore', 'freelance']",
     *   description="Company type"
     * ),
     * @OA\Property(
     *   property="company_description",
     *   type="string",
     *   description="Company description"
     * ),
     * @OA\Property(
     *   property="website",
     *   type="string",
     *   description="Company URL"
     * ),
     * @OA\Property(
     *   property="image_foto",
     *   type="string",
     *   description="Base64 encoded profile image"
     * ),
     * @OA\Property(
     *   property="image_logo",
     *   type="string",
     *   description="Base64 encoded logo image"
     * ),
 * ),
 */
class BeamerUpdate extends FormRequest
{
    public function rules()
    {
        return [
            'company_name' => 'required',
            'my_language'=>'required',
            // 'email' => 'required|email|unique:users,email|max:50',
            'name' => 'required',
            'surname' => 'required',
            'store_address' => 'required',
            'store_postal_code'=>'required',
            'store_city'=>'required',
            'store_country'=>'required',
            'phone' => 'required',
            'company_doc'=>'required',
            'company_description'=>'',
            'website'=>'url',
            'company_type'=>[
                'required',
                Rule::in(['instore','freelance']),
            ],
            'password' => 'min:6',
            'accept_parcel_return' => '',
            'level_expertise' => '',
            'second_hand_resaler' => '',
            // 'c_password' => 'required|same:password',
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
            'email.required' => 'Email is required',
            'email.email' => 'Email is not correct',
            'email.unique' => 'Email already registered',
            'website.url' => 'Invalid URL',
        ];
    }
}
