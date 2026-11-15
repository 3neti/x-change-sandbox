<?php

namespace App\Http\Requests\Settings;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PipedreamConnectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'app' => [
                'required',
                'string',
                'max:80',
                Rule::in(config('pipedream.allowed_apps', [])),
            ],
        ];
    }

    public function appSlug(): string
    {
        $app = $this->validated('app');

        return is_string($app) ? $app : '';
    }
}
