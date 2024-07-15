<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

/*
postman format

name:Test name
last_name:Last name
email:edinhoalmeida@gmail.com
password:123456

*/

/**
* @OA\Schema(
*   schema="clientrequestsimple",
* @OA\Property(
*   property="name",
*   type="string",
*   description="Name"
* ),
* @OA\Property(
*   property="surname",
*   type="string",
*   description="Last name"
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
*   property="firebase_token",
*   type="string",
*   description="Firebase device token",
*  ),
* ),
*/
class ClientRequestSimple extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|min:6'
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
