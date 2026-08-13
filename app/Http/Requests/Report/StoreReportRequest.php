<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', Rule::in(['spam', 'fraud', 'prohibited', 'duplicate', 'other'])],
        ];
    }
}
