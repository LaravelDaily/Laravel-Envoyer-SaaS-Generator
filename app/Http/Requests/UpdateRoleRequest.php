<?php

namespace App\Http\Requests;

use App\Role;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;

    }

    public function rules()
    {
        return [
            'title'         => [
                'required'],
            'price'         => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647'],
            'permissions.*' => [
                'integer'],
            'permissions'   => [
                'required',
                'array'],
        ];

    }
}
