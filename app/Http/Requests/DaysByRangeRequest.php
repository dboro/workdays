<?php

namespace App\Http\Requests;

class DaysByRangeRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start']
        ];
    }
}
