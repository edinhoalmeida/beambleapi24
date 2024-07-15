<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class RatingRequest extends FormRequest
{
 
    public function rules()
    {
        return [
            'rating' => 'required|numeric|between:1,5',
            // 'evaluation' => [
            //     'required',
            //     Rule::in(['good', 'bad']),
            // ],
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
            'evaluation.*' => "Evaluation is required, string 'good' or 'bad'",
            'rating.required' => 'Rating is required, numeric and between 1 and 5',
            'rating.numeric' => 'Rating is required, numeric and between 1 and 5',
            'rating.between' => 'Rating is required, numeric and between 1 and 5',
        ];
    }
}
