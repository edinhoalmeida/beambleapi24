<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
 
    public function rules()
    {
        return [
            'name' => 'required',
            'surname' => 'required',
            // 'image' => 'required',
            'interface_as' => [
                // 'required',
                Rule::in(['beamer', 'client']),
            ],
            'minibio' => 'required',
            'tos_accepted'=>'required',
            'languages'=>'required',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|min:6',
            // 'password' => 'required',
            'confirm_password' => 'required|same:password',
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
