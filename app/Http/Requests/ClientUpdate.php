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
 *   schema="clientupdate",
     * @OA\Property(
     *   property="name",
     *   type="string",
     *   description="First name"
     * ),
     * @OA\Property(
     *   property="suname",
     *   type="string",
     *   description="Last name"
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
 * ),
 */
class ClientUpdate extends FormRequest
{
    public function rules()
    {
        return [
            'name' => '',
            'surname' => 'required_with:name',
            'my_language'=>'',
            // 'email' => 'required|email|unique:users,email|max:50',
            'password' => 'min:6',
            'phone' => '',
            // 'password' => 'required',
            'address' => '',
            'postal_code' => 'required_with:address',
            'city' => 'required_with:address',
            'country' => 'required_with:address',
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
