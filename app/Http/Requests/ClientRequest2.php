<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/*
postman format

name:Test name
my_language[]:fr
my_language[]:en
email:edinhoalmeida@gmail.com
password:123456
phone:551199293123
address:Rua coronel oscar porto, 932
postal_code:04003-005
city:São Paulo
country:Brasil
tos_accepted:yes
mailing:0 or 1

*/

/**
 * @OA\Schema(
 *   schema="clientrequest2",
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="Full name"
     * ),
     * @OA\Property(
     *   property="my_language[]",
     *   type="string",
     *   description="Language. It can be my_language:fr also."
     * ),
     * @OA\Property(
     *   property="email",
     *   type="string",
     *   description="Email"
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
     *   property="address",
     *   type="string",
     *   description="User address"
     * ),
     * @OA\Property(
     *   property="postal_code",
     *   type="string",
     *   description="User postal_code"
     * ),
     * @OA\Property(
     *   property="city",
     *   type="string",
     *   description="User city"
     * ),
     * @OA\Property(
     *   property="country",
     *   type="string",
     *   description="User country"
     * ),
     * @OA\Property(
     *   property="tos_accepted",
     *   type="string",
     *   description="Terms of service. should be a string 'yes'"
     * ),
*    @OA\Property(
*       property="firebase_token",
*       type="string",
*       description="Firebase device token",
*     ),
 * ),
 */
class ClientRequest2 extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'my_language'=>'required',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|min:6',
            'phone' => 'required',
            // 'password' => 'required',
            'address' => 'required',
            'postal_code' => 'required',
            'city' => 'required',
            'country' => 'required',
            'tos_accepted' => 'required',
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
        ];
    }
}
