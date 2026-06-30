<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\NotificationCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(NotificationCategory::class)],
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'cta_url' => ['nullable', 'url'],
        ];
    }

    public function category(): NotificationCategory
    {
        return NotificationCategory::from($this->validated('category'));
    }
}
