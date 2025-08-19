<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
            'first_name'      => 'required|string|max:191',
            'last_name'       => 'required|string|max:191',
            'email'           => 'required|email|unique:employees,email',
            'phone'           => 'nullable|string|max:50',
            'id_number'       => 'required|string|max:191',
            'birthdate'       => 'required|date',
            'start_date'      => 'nullable|date',
            'pay_frequency'   => 'required|in:monthly,weekly,biweekly',
            'payment_method'  => 'required|in:bank,cash,cheque',
            'status'          => 'required|in:active,inactive',
        ];
    }
}
