<?php

namespace App\Http\Requests;

use App\Models\Client;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // no login in this project
    }

    /** Trim the text inputs and store e-mail in lower case before validating. */
    protected function prepareForValidation(): void
    {
        $name  = $this->input('name');
        $email = $this->input('email');
        $phone = $this->input('phone');

        $this->merge([
            'name'  => is_string($name) ? trim($name) : $name,
            'email' => is_string($email) ? Str::lower(trim($email)) : $email,
            'phone' => is_string($phone) ? trim($phone) : $phone,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:100'],
            'email'        => [
                'required', 'email', 'max:150',
                // On update the route-bound Client is ignored. The id is taken
                // from the URL model, never from the submitted form data.
                Rule::unique('clients', 'email')->ignore($this->route('client')),
            ],
            'phone'        => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'company_name' => ['nullable', 'string', 'max:150'],
            'address'      => ['nullable', 'string', 'max:1000'],
            'city'         => ['nullable', 'string', 'max:80'],
            'status'       => ['required', Rule::in(Client::STATUSES)],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'         => 'full name',
            'email'        => 'e-mail address',
            'phone'        => 'phone number',
            'company_name' => 'company name',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'A client with this e-mail address already exists.',
            'phone.regex'  => 'The phone number must contain 10 to 15 digits (numbers only).',
        ];
    }
}
