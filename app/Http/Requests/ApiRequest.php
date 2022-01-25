<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors(),
                'message' => __('app.invalid_values')
            ], 422)
        );
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            response()->json([
                'message' => __('app.action_unauthorized')
            ], 403)
        );
    }
}
