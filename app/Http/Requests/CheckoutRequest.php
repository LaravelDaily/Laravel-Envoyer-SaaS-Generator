<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'checkout_plan_id' => 'required|integer|exists:roles,id',
            'billing_name'     => 'required',
            'address_1'        => 'required',
            'country_id'       => 'required|integer|exists:countries,id',
            'city'             => 'required',
            'postcode'         => 'required',
        ];
    }
}
