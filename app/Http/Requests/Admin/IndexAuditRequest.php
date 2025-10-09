<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('super_admin') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => $this->filled('user_id') ? (int) $this->input('user_id') : null,
            'event' => $this->filled('event') ? (string) $this->input('event') : null,
            'auditable_type' => $this->filled('auditable_type') ? (string) $this->input('auditable_type') : null,
            'search' => $this->filled('search') ? (string) $this->input('search') : null,
            'date_from' => $this->filled('date_from') ? (string) $this->input('date_from') : null,
            'date_to' => $this->filled('date_to') ? (string) $this->input('date_to') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'event' => ['nullable', 'string', 'max:100'],
            'auditable_type' => ['nullable', 'string', 'max:150'],
            'search' => ['nullable', 'string', 'max:255'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', Rule::in([15, 25, 50, 100])],
        ];
    }

    public function filters(): array
    {
        $validated = $this->validated();

        return [
            'user_id' => $validated['user_id'] ?? null,
            'event' => $validated['event'] ?? null,
            'auditable_type' => $validated['auditable_type'] ?? null,
            'search' => $validated['search'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? 25,
        ];
    }
}
