<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/*

Nome
Sobrenome
Masculino/feminino
email
Localização para serviço (input endereço)
Endereço pessoal, cidade, país
Telefone
Número de identidade
Foto
Conexão stripe ID
Idioma utilizado para comunicação na experiência

*/
class BeamerRequest extends FormRequest
{
 
    public function rules()
    {
        return [
            'name' => 'required',
            'surname' => 'required',
            'gender' => 'required',
            'beaming_street' => 'required',
            'contact_address'=>'required',
            'contact_city'=>'required',
            'contact_country'=>'required',
            'phone' => 'required',
            'doc_id'=>'required',
            'type' =>  [
                'required',
                Rule::in(['beamer']),
            ],
            'my_language'=>'required',
            'email' => 'required|email|unique:users,email|max:50',
            'password' => 'required|min:6',
            // 'password' => 'required',
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
