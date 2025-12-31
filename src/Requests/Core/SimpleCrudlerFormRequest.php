<?php

namespace Crudler\Requests\Core;

use Illuminate\Foundation\Http\FormRequest;

class SimpleCrudlerFormRequest extends FormRequest
{
    protected array $rules = [];
    protected array $messages = [];
    protected bool $authorizeFlag = true;

    public function __construct(array $config = [])
    {
        parent::__construct();

        $this->rules = $config['rules'] ?? [];
        $this->messages = $config['messages'] ?? [];
        $this->authorizeFlag = $config['authorize'] ?? true;
    }

    public function rules(): array
    {
        return $this->rules;
    }

    public function messages(): array
    {
        return $this->messages;
    }

    public function authorize(): bool
    {
        return $this->authorizeFlag;
    }
}
