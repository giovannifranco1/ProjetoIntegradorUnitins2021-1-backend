<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class AuthenticateRequest extends FormRequest
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
        $validator = Validator::make([
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(['message' => $validator->errors()]);
        }
        return $validator;
    }
}
